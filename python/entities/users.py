from sqlalchemy import create_engine, Column, Integer, String, Boolean
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship
from .base import Base

class User(Base):
    __tablename__ = 'users'
    id = Column(Integer, primary_key=True, autoincrement=True)
    username = Column(String(255), nullable=False)
    email = Column(String(255), nullable=False, unique=True)
    password = Column(String(255), nullable=True)
    is_verified = Column(Boolean, default=False, nullable=True)
    verification_code = Column(String(6), nullable=True)

    documents = relationship("Document", back_populates="author")
    shared_documents = relationship("Document", secondary="document_user_association", back_populates="shared_with")
    processing_results_created = relationship("ProcessingResult", back_populates="created_by")
