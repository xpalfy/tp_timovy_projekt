from datetime import datetime
from typing import TYPE_CHECKING, List
import os
import shutil
import json
from sqlalchemy.orm import Session
from entities.document import Document, DocumentType
from entities.processing_result import ProcessingResult, ProcessingStatus
from entities.item import Item
from entities.users import User
from controller.db_controller import get_db_session
from modules.matcher import get_cipher_key_match
if TYPE_CHECKING:
    from entities.users import User

status_messages = {'uploaded': 'File uploaded successfully',
                   'segmented': 'File segmented successfully',
                   'classified': 'File analyzed successfully',
                   'processed': 'Letters segmented successfully',
                   'saved': 'File saved successfully'}

class DocumentService:
    def __init__(self, db: Session=None):
        if db is None:
            self.db = next(get_db_session())
        self.db = db

    def get_document_by_id_and_author(self, document_id: int, author_id: int) -> Document | None:
        return self.db.query(Document).filter_by(id=document_id, author_id=author_id).first()
    
    def get_document_by_id(self, document_id: int):
        return self.db.query(Document).filter_by(id=document_id).first()
    
    def get_item_by_id_and_document_id(self, item_id: int, document_id: int) -> Item | None:
        return self.db.query(Item).filter_by(id=item_id, document_id=document_id).first()
    
    def get_document_id_and_user_id(self, document_id: int, user_id: int) -> Document | None:
        doc = self.get_document_by_id(document_id)
        user = self.db.query(User).filter_by(id=user_id).first()
        if not doc:
            return None
        if doc.author_id == user_id:
            return doc
        if doc.is_public:
            return doc
        if user in doc.shared_with:
            return doc
        return None
    
    def document_name_exists(self, name: str, author_id: int, exclude_id: int = None) -> bool:
        query = self.db.query(Document).filter(Document.title == name, Document.author_id == author_id)
        if exclude_id:
            query = query.filter(Document.id != exclude_id)
        return self.db.query(query.exists()).scalar()

    def update_document_title(self, document: Document, new_title: str, user_id: int, folder:str = None):
        user = self.db.query(User).filter_by(id=user_id).first() 
        if not user:
            raise Exception("User not found")

        user_name = user.username

        old_path = os.path.join('..',folder,'DOCS', user_name, document.doc_type.name, document.title)
        new_path = os.path.join('..',folder,'DOCS', user_name, document.doc_type.name, new_title)

        if os.path.exists(old_path) and os.access(old_path, os.W_OK):
            os.rename(old_path, new_path)
        else:
            print(f"Old path: {old_path}")
            print(f"New path: {new_path}")
            print(f"Exists: {os.path.exists(old_path)}")
            print(f"Writable: {os.access(old_path, os.W_OK)}")
            raise Exception('Invalid path or not writable')

        document.title = new_title

    def update_shared_users(self, document: Document, shared_usernames: list[str]):
        document.shared_with.clear()

        for username in shared_usernames:
            user = self.db.query(User).filter_by(username=username.strip()).first()
            if user:
                document.shared_with.append(user)

    def save_changes(self):
        self.db.commit()
    
    def delete_document(self, document_id: int, user_id: int, folder: str = None):
        document = self.get_document_by_id_and_author(document_id, user_id)
        if not document:
            raise Exception("Document not found")

        user = self.db.query(User).filter_by(id=user_id).first()
        if not user:
            raise Exception("User not found")

        doc_directory = os.path.join('..', folder, 'DOCS', user.username, document.doc_type.name, document.title)

        # Delete processing results associated with items
        if hasattr(document, 'items') and document.items:
            for item in document.items:
                if hasattr(item, 'processing_results') and item.processing_results:
                    for result in item.processing_results:
                        self.db.delete(result)
                self.db.delete(item)

        # Delete the document itself
        self.db.delete(document)
        self.db.commit()

        # Remove the document directory if it exists
        if os.path.isdir(doc_directory):
            shutil.rmtree(doc_directory)

    def edit_public(self, document: Document, public: bool):
        document.is_public = public
        self.db.commit()
    
    def add_shared_user(self, document_id: int, shared_username: str):
        document = self.db.query(Document).filter_by(id=document_id).first()
        if not document:
            raise Exception("Document not found")
        user = self.db.query(User).filter_by(username=shared_username.strip()).first()
        if not user:
            raise Exception("User not found")
        if user == document.author:
            raise Exception("Cannot share document with the author")
        document.shared_with.append(user)
        self.db.commit()
    
    def remove_shared_user(self, document_id: int, shared_username: str):
        document = self.db.query(Document).filter_by(id=document_id).first()
        if not document:
            raise Exception("Document not found")
        user = self.db.query(User).filter_by(username=shared_username.strip()).first()
        if not user:
            raise Exception("User not found")
        document.shared_with.remove(user)
        self.db.commit()
    
    def get_json_from_db(self, document_id, user_id):
        doc: Document = self.db.query(Document).filter_by(id=document_id).first()
        user: User = self.db.query(User).filter_by(id=user_id).first()
        if not doc:
            raise Exception("Document not found")
        if not user:
            raise Exception("User not found")
        if user not in doc.shared_with and user != doc.author and not doc.is_public:
            raise Exception("User does not have access to this document")
        if not doc.items:
            raise Exception("No items found for this document")
        item: Item = doc.items[0]
        if not item.processing_results:
            raise Exception("No processing results found for this item")
        processing_result: ProcessingResult = item.processing_results[-1]
        if not processing_result:
            raise Exception("Processing result not found")
        return processing_result.result

    def save_json_to_db(self, document_id, user_id, json_data):
        doc: Document = self.db.query(Document).filter_by(id=document_id).first()
        user: User = self.db.query(User).filter_by(id=user_id).first()
        if not doc:
            raise Exception("Document not found")
        if not user:
            raise Exception("User not found")
        if not isinstance(json_data, dict):
            raise Exception("Invalid JSON data")
        if user not in doc.shared_with and user != doc.author and not doc.is_public:
            raise Exception("User does not have access to this document")
        if not doc.items:
            raise Exception("No items found for this document")
        item: Item = doc.items[0]
        if not item.processing_results:
            raise Exception("No processing results found for this item")
        processing_result: ProcessingResult = item.processing_results[0]
        if not processing_result:
            raise Exception("Processing result not found")
        processing_result.result = json_data
        self.db.commit()

    def get_image_paths_by_document_id(self, document_id: int) -> list[str]:
        doc = self.get_document_by_id(document_id)
        if not doc:
            raise Exception("Document not found")
        if not doc.items:
            raise Exception("No items found for this document")
        item: Item = doc.items[0]
        if not item.processing_results:
            raise Exception("No processing results found for this item")
        return item.image_path

    def get_shared_users_by_document_id(self, document_id, user_id=None) -> list[str]:
        doc = self.get_document_by_id(document_id)
        user = self.db.query(User).filter_by(id=user_id).first() if user_id else None
        if not doc:
            raise Exception("Document not found")
        if not doc.shared_with:
            raise Exception("No shared users found for this document")
        return [u.username for u in doc.shared_with if u.username != user.username and u.username != doc.author.username] if user else [u.username for u in doc.shared_with] if doc.shared_with else []
    
    def get_publish_date_by_document_id(self, document_id: int) -> str:
        doc = self.get_document_by_id(document_id)
        if not doc:
            raise Exception("Document not found")
        if not doc.items:
            raise Exception("No items found for this document")
        item: Item = doc.items[-1]
        return item.publish_date
    
    def save_processing_result(self, data: dict) -> str:

        document_id = int(data['document_id'])
        item_id = int(data['item_id'])
        status_str = str(data['status']).lower()
        user_id = int(data['user_id'])
        model_used = 'MODEL1'
        result_json = data.get('json_data')
        polygons = data.get('polygons')
        if polygons and result_json is None:
            result_json = {
                'polygons': polygons
            }
        
        #check if status is valid
        if status_str not in [status.value for status in ProcessingStatus]:
            raise ValueError("Invalid status")
        
        doc = self.get_document_by_id(document_id)
        if not doc:
            raise Exception("Document not found")
        user = self.db.query(User).filter_by(id=user_id).first()
        if not user:
            raise Exception("User not found")
        if doc.author_id != user_id and user not in doc.shared_with:
            raise Exception("User does not have access to this document")
        if not doc.items:
            raise Exception("No items found for this document")
        item = self.get_item_by_id_and_document_id(item_id, document_id)
        if not item:
            raise Exception("Item not found")
        
        new_status = ProcessingStatus(status_str)

        now = datetime.utcnow().isoformat()
        proc_result = ProcessingResult(
            item_id=item_id,
            status=new_status,
            message=status_messages[status_str],
            model_used=model_used,
            created_date=now,
            modified_date=now,
            result=result_json,
            created_by_id=user_id,
            created_by=user
        )
        item.processing_results.append(proc_result)
        item.modified_date = now
        if item.status < new_status:
            item.status = new_status
        self.db.add(proc_result)
        self.db.commit()   
    
    def get_documents_by_user_id_and_status(self, user_id: int, status: ProcessingStatus) -> list[Document] | None:
        user = self.db.query(User).filter_by(id=user_id).first()
        if not user:
            raise Exception("User not found")
        documents = self.db.query(Document).filter((Document.author_id == user_id) | (Document.shared_with.any(id=user_id))).all()
        if not documents:
            raise Exception("No documents found for this user")
        filtered_documents = []
        for doc in documents:
            if doc.author_id != user_id and user not in doc.shared_with:
                continue
            if doc.items:
                for item in doc.items:
                    if item.status.name == status:
                        filtered_documents.append(doc)
                        break
        if not filtered_documents:
            raise Exception("No documents found with this status and user access")
        return filtered_documents
        
        
    def get_items_by_document_id_and_status(self, document_id: int, status: ProcessingStatus, user_id: int) -> list[Item] | None:
        doc = self.get_document_by_id(document_id)
        user = self.db.query(User).filter_by(id=user_id).first()
        if not doc:
            raise Exception("Document not found")
        if not user:
            raise Exception("User not found")
        if doc.author_id != user_id and user not in doc.shared_with:
            raise Exception("User does not have access to this document")
        if not doc.items:
            raise Exception("No items found for this document")
        filtered_items = []
        for item in doc.items:
            if item.status.name == status:
                filtered_items.append(item)
        if not filtered_items:
            raise Exception("No items found with this status and user access")
        return filtered_items
    
    def delete_user_documents(self, user_id: int, folder: str = None):
        user = self.db.query(User).filter_by(id=user_id).first()
        if not user:
            raise Exception("User not found")
        documents = self.db.query(Document).filter_by(author_id=user_id).all()
        if not documents:
            raise Exception("No documents found for this user")
        for doc in documents:
            self.delete_document(doc.id, user_id, folder)
            
    def get_all_keys_for_user(self, user: User) -> List[dict]:
        documents = self.db.query(Document).filter_by(doc_type=DocumentType.KEY).all()
        if not documents:
            raise Exception("No documents found")
        keys = []
        for doc in documents:
            if doc.author_id != user.id and user not in doc.shared_with and not doc.is_public:
                continue
            if doc.doc_type == DocumentType.KEY:
                if not doc.items:
                    raise Exception("No items found for this document")
                item: Item = doc.items[-1]
                if not item.processing_results or item.status != ProcessingStatus.PROCESSED:
                    raise Exception("No processing results found for this item")
                keys.append({'document_id': doc.id, 'title': doc.title, 'key': item.processing_results[-1].result, 
                             'image_path': item.image_path, 'status': item.status.name})
        
        
        

    def get_keys_for_cipher(self, document_id: int, user_id: int) -> dict:
        doc = self.get_document_by_id(document_id)
        user = self.db.query(User).filter_by(id=user_id).first()
        if not doc:
            raise Exception("Document not found")
        if not user:
            raise Exception("User not found")
        if doc.author_id != user_id and user not in doc.shared_with:
            raise Exception("User does not have access to this document")
        if doc.doc_type != DocumentType.CIPHER:
            raise Exception("Document is not a cipher")
        if not doc.items:
            raise Exception("No items found for this document")
        item: Item = doc.items[-1]
        if not item.processing_results:
            raise Exception("No processing results found for this item")
        
        keys = self.get_all_keys_for_user(user)
        if not keys:
            raise Exception("No keys found for this user")
        
        keys = get_cipher_key_match(doc, keys)
        if not keys:
            raise Exception("No keys found for this cipher")
        return keys
    
    def get_processing_result_status(self, document_id, user_id):
        doc = self.get_document_by_id(document_id)
        user: User = self.db.query(User).filter_by(id=user_id).first()
        if not doc:
            raise Exception("Document not found")
        if not user:
            raise Exception("User not found")
        if doc.author_id != user.id and user not in doc.shared_with:
            raise Exception("User does not have access to this document")
        if not doc.items:
            raise Exception("No items found for this document")
        item: Item = doc.items[-1]
        if not item.processing_results:
            raise Exception("No processing results found for this item")
        need_continue = item.status != ProcessingStatus.SAVED
        return {"need_continue": need_continue, "status": item.status.name}
