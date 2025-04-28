import enum
from sqlalchemy import create_engine, Column, Integer, String, Enum, Table, ForeignKey
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship
from entities.users import User
from entities.tag import Tag
from entities.item import Item
from typing import List
from .base import Base


# Many-to-many association table for shared_with
document_user_association = Table(
    "document_user_association",
    Base.metadata,
    Column("document_id", Integer, ForeignKey("documents.id"), primary_key=True),
    Column("user_id", Integer, ForeignKey("users.id"), primary_key=True),
)

document_tag_association = Table(
    "document_tag_association",
    Base.metadata,
    Column("document_id", Integer, ForeignKey("documents.id"), primary_key=True),
    Column("tag_id", Integer, ForeignKey("tags.id"), primary_key=True),
)

class DocumentType(enum.Enum):
    CIPHER = 'cipher'
    KEY = 'key'
    TBD = 'tbd'
    tmp = 'tmp'

class DocumentStatus(enum.Enum):
    ACTIVE = 'active'
    INACTIVE = 'inactive'
    DELETED = 'deleted'
    

class Document(Base):
    __tablename__ = 'documents'
    id: int = Column(Integer, primary_key=True, autoincrement=True)
    author_id: int = Column(Integer, ForeignKey('users.id'))
    author: User = relationship("User", back_populates="documents")
    
    title: str = Column(String(255))
    doc_type: DocumentType = Column(Enum(DocumentType, name='document_types'))
    status: DocumentStatus = Column(Enum(DocumentStatus, name='document_status'))
    description: str = Column(String(255))
    
    shared_with: List["User"] = relationship("User", secondary=document_user_association, back_populates="shared_documents")
    
    tags: List["Tag"] = relationship("Tag", secondary=document_tag_association, back_populates="documents")
    items: List["Item"] = relationship("Item", back_populates="document")

    
    
    