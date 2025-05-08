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


class UserService:
    def __init__(self, db: Session=None):
        if db is None:
            self.db = next(get_db_session())
        self.db = db
    def get_user_name_by_id(self, user_id: int) -> str:
        user = self.db.query(User).filter_by(id=user_id).first()
        if user:
            return user.username
        return None
    
    def delete_user(self, user_id: int) -> bool:
        user = self.db.query(User).filter_by(id=user_id).first()
        if user:
            self.db.delete(user)
            self.db.commit()
            return True
        return False
    