# services/document_service.py

import os
import shutil
from sqlalchemy.orm import Session
from entities.document import Document
from entities.users import User
from controller.db_controller import get_db_session


class DocumentService:
    def __init__(self, db: Session=None):
        if db is None:
            self.db = next(get_db_session())
        self.db = db

    def get_document_by_id_and_author(self, document_id: int, author_id: int) -> Document | None:
        return self.db.query(Document).filter_by(id=document_id, author_id=author_id).first()

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
        # If you also store the path in DB, update it here too: document.path = new_path

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

        # Save directory path before deleting the document
        doc_directory = os.path.join('..', folder, 'DOCS', user.username, document.doc_type.name, document.title)

        # First, delete associated items if you have a relationship (assuming cascade delete isn't set)
        if hasattr(document, 'items') and document.items:
            for item in document.items:
                self.db.delete(item)

        self.db.delete(document)
        self.db.commit()

        # Delete the associated directory and its contents
        if os.path.isdir(doc_directory):
            shutil.rmtree(doc_directory)

    def edit_public(self, document: Document, public: bool):
        document.public = public
        self.db.commit()

    def add_shared_user(self, document_id: int, shared_username: str):
        document = self.db.query(Document).filter_by(id=document_id).first()
        if not document:
            raise Exception("Document not found")
        user = self.db.query(User).filter_by(username=shared_username.strip()).first()
        if not user:
            raise Exception("User not found")
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
        
