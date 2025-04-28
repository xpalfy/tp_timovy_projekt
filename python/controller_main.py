from sqlalchemy import create_engine, Column, Integer, String
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from entities.users import User
from entities.base import Base
from entities.tag import Tag
from entities.item import Item
from entities.document import Document, DocumentType, DocumentStatus
from entities.processing_result import ProcessingResult, ProcessingStatus, Model

# THIS IS A TEST FILE DO_NOT_USE
# Database credentials from config.php
db_username = "root"
db_password = "NemTudokJobbJelszavat123."
db_host = "localhost"
db_name = "TP_timovy_projekt"

# Construct the DATABASE_URL
DATABASE_URL = f"mysql+pymysql://{db_username}:{db_password}@{db_host}/{db_name}"

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(bind=engine)
Base.metadata.create_all(engine)

session = SessionLocal()

# Utility function to get or create a record
def get_or_create(session, model, defaults=None, **kwargs):
    instance = session.query(model).filter_by(**kwargs).first()
    if instance:
        return instance
    else:
        instance = model(**kwargs, **(defaults or {}))
        session.add(instance)
        session.commit()
        return instance

# Get or create users
alice = get_or_create(session, User, username="Alice", email="alice@example.com")
bob = get_or_create(session, User, username="Bob", email="bob@example.com")

# alice_copy: User = session.query(User).filter_by(username="Alice").first()

# Get or create tags
tag1 = get_or_create(session, Tag, name="Work")
tag2 = get_or_create(session, Tag, name="Important")

# Create a document with an author
# Get or create document
doc: Document = session.query(Document).filter_by(title="Project Plan", author=alice).first()
if doc:
    doc.status = DocumentStatus.INACTIVE
    doc.doc_type = DocumentType.KEY
    doc.description = "This is a key document"
    doc.author = bob
    doc.shared_with = [alice]

else:
    doc = Document(
        title="Project Plan",
        author=alice,
        status=DocumentStatus.ACTIVE,
        doc_type=DocumentType.CIPHER,
    )
    session.add(doc)

item1 = Item(
    title="Item 1",
    description="Description 1",
    status="uploaded",
    document=doc
)
processing_result = ProcessingResult(
    item_id=item1.id,  # Associate with the created item
    status=ProcessingStatus.UPLOADED,  # Set the initial status
    message="Processing started successfully.",
    model_used=Model.MODEL1,  # Specify the model used
    created_date="2025-04-28",
    modified_date="2025-04-28",
    result={"key": "value"}  # Example JSON result
)
session.add(processing_result)
session.commit()

# Query all documents with a specific tag
important_docs = session.query(Document).join(Document.tags).filter(Tag.name == "Important").all()
print([d.title for d in important_docs])  # Output: ['Project Plan']