from sqlalchemy import create_engine, Column, Integer, String
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship
from .base import Base

class User(Base):
    __tablename__ = 'users_python'
    id = Column(Integer, primary_key=True, autoincrement=True)
    username = Column(String(255))
    email = Column(String(255), unique=True)
    password = Column(String(255))
    documents = relationship("Document", back_populates="author")
    shared_documents = relationship("Document", secondary="document_user_association", back_populates="shared_with")

