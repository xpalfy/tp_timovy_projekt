import enum
from sqlalchemy import create_engine, Column, Integer, String, Enum, Table, ForeignKey
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship
from entities.users import User
from .base import Base


# Many-to-many association table for shared_with
document_user_association = Table(
    "document_user_association",
    Base.metadata,
    Column("document_id", Integer, ForeignKey("documents.id"), primary_key=True),
    Column("user_id", Integer, ForeignKey("users_python.id"), primary_key=True),
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

class DocumentStatus(enum.Enum):
    ACTIVE = 'active'
    INACTIVE = 'inactive'
    DELETED = 'deleted'
    

class Document(Base):
    __tablename__ = 'documents'
    id = Column(Integer, primary_key=True, autoincrement=True)
    author_id = Column(Integer, ForeignKey('users_python.id'))
    author = relationship("User", back_populates="documents")
    
    title = Column(String(255))
    doc_type = Column(Enum(DocumentType, name='document_types'))
    status = Column(Enum(DocumentStatus, name='document_status'))
    description = Column(String(255))
    
    shared_with = relationship("User", secondary=document_user_association, back_populates="shared_documents")
    
    tags = relationship("Tag", secondary=document_tag_association, back_populates="documents")

    
    
    