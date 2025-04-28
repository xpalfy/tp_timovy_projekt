# services/document_service.py

import os
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

    def update_document_title(self, document: Document, new_title: str, user_id: int):
        user = self.db.query(User).filter_by(id=user_id).first()
        if not user:
            raise Exception("User not found")

        user_name = user.username  # Retrieve the username

        old_path = os.path.join('DOCS', user_name, document.doc_type.name, document.title)
        new_path = os.path.join('DOCS', user_name, document.doc_type.name, new_title)

        if os.path.exists(old_path) and os.access(old_path, os.W_OK):
            os.rename(old_path, new_path)
        else:
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
