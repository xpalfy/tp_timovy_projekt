from sqlalchemy import Column, Integer, String
from sqlalchemy.orm import relationship
from .base import Base

class Tag(Base):
    __tablename__ = "tags"

    id = Column(Integer, primary_key=True)
    name = Column(String(50), unique=True)

    # Many-to-Many with Document
    documents = relationship("Document", secondary="document_tag_association", back_populates="tags")