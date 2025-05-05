from typing import TYPE_CHECKING
import os
import shutil
import json
from sqlalchemy.orm import Session
from entities.document import Document, DocumentType
from entities.users import User
from controller.db_controller import get_db_session
if TYPE_CHECKING:
    from entities.users import User
    from entities.item import Item
    from entities.processing_result import ProcessingResult


class DocumentService:
    def __init__(self, db: Session=None):
        if db is None:
            self.db = next(get_db_session())
        self.db = db

    def get_document_by_id_and_author(self, document_id: int, author_id: int) -> Document | None:
        return self.db.query(Document).filter_by(id=document_id, author_id=author_id).first()
    
    def get_document_by_id(self, document_id: int):
        return self.db.query(Document).filter_by(id=document_id).first()
    
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
    
    def delete_document(self, document_id: int, user_id: int, folder:str = None):
        document = self.get_document_by_id_and_author(document_id, user_id)
        if not document:
            raise Exception("Document not found")

        user = self.db.query(User).filter_by(id=user_id).first()
        if not user:
            raise Exception("User not found")

        doc_directory = os.path.join('..', folder, 'DOCS', user.username, document.doc_type.name, document.title)

        if hasattr(document, 'items') and document.items:
            for item in document.items:
                self.db.delete(item)

        self.db.delete(document)
        self.db.commit()

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
    
    def get_key_json(self, document_id, user_id):
        doc: Document = self.db.query(Document).filter_by(id=document_id).first()
        user: User = self.db.query(User).filter_by(id=user_id).first()
        if not doc:
            raise Exception("Document not found")
        if not user:
            raise Exception("User not found")
        if doc.doc_type != DocumentType.KEY:
            raise Exception("Document is not a key document")
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
        return processing_result.result

    def save_key_json(self, document_id, user_id, json_data):
        doc: Document = self.db.query(Document).filter_by(id=document_id).first()
        user: User = self.db.query(User).filter_by(id=user_id).first()
        if not doc:
            raise Exception("Document not found")
        if not user:
            raise Exception("User not found")
        if not isinstance(json_data, dict):
            raise Exception("Invalid JSON data")
        if doc.doc_type != DocumentType.KEY:
            raise Exception("Document is not a key document")
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
