from __future__ import annotations
from typing import TYPE_CHECKING
import enum
from sqlalchemy import create_engine, Column, Integer, String, Enum, Table, ForeignKey
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship
from .base import Base
from entities.processing_result import ProcessingResult, ProcessingStatus



if TYPE_CHECKING:
    from entities.document import Document



class Item(Base):
    __tablename__ = 'items'
    id = Column(Integer, primary_key=True, autoincrement=True)
    document_id = Column(Integer, ForeignKey('documents.id'))
    document: Document = relationship("Document", back_populates="items")
    status = Column(Enum(ProcessingStatus, name='processing_status'))
    title = Column(String(255))
    description = Column(String(255))
    image_path = Column(String(255))
    publish_date = Column(String(255))
    modified_date = Column(String(255))
    processing_results = relationship("ProcessingResult", back_populates="item")
    
    
    
    