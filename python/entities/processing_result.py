from __future__ import annotations
from typing import TYPE_CHECKING
import enum
from sqlalchemy import create_engine, Column, Integer, String, Enum, Table, ForeignKey, JSON
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship
from .base import Base

if TYPE_CHECKING:
    from entities.item import Item
    

class ProcessingStatus(enum.Enum):
    ERROR = 'error'
    UPLOADED = 'uploaded'
    SEGMENTED = 'segmented'
    CLASSIFIED = 'classified'
    PROCESSED = 'processed'
    EXTRACTED = 'extracted'
    SAVED = 'saved'
    def __gt__(self, other):
        if self.__class__ is other.__class__:
            # Compare based on the order of definition
            return list(self.__class__).index(self) > list(self.__class__).index(other)
        return NotImplemented

    def __lt__(self, other):
        if self.__class__ is other.__class__:
            # Compare based on the order of definition
            return list(self.__class__).index(self) < list(self.__class__).index(other)
        return NotImplemented
    
class Model(enum.Enum):
    MODEL1 = 'model1'
    MODEL2 = 'model2'
    MODEL3 = 'model3'
    MODEL4 = 'model4'
    
class ProcessingResult(Base):
    
    __tablename__ = 'processing_results'
    id = Column(Integer, primary_key=True, autoincrement=True)
    item_id = Column(Integer, ForeignKey('items.id'))
    status = Column(Enum(ProcessingStatus, name='processing_status'))
    message = Column(String(255))
    model_used = Column(Enum(Model, name='model_used'))
    created_date = Column(String(255))
    modified_date = Column(String(255))
    result = Column(JSON)
    created_by_id = Column(Integer, ForeignKey('users.id'), nullable=False)

    item: Item = relationship("Item", back_populates="processing_results")
    created_by = relationship("User", back_populates="processing_results_created")