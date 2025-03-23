from sqlalchemy import create_engine, Column, Integer, String
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from entities.users import User
from entities.base import Base
from entities.tag import Tag
from entities.document import Document


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

# Get or create tags
tag1 = get_or_create(session, Tag, name="Work")
tag2 = get_or_create(session, Tag, name="Important")

# Create a document with an author
# Get or create document
doc = session.query(Document).filter_by(title="Project Plan", author=alice).first()
if not doc:
    doc = Document(title="Project Plan", description="Details about the project", author=alice)
    session.add(doc)
    session.commit()

# Ensure Bob is in shared_with
if bob not in doc.shared_with:
    doc.shared_with.append(bob)

# Ensure tags are assigned
for tag in [tag1, tag2]:
    if tag not in doc.tags:
        doc.tags.append(tag)

session.commit()

# Query all documents with a specific tag
important_docs = session.query(Document).join(Document.tags).filter(Tag.name == "Important").all()
print([d.title for d in important_docs])  # Output: ['Project Plan']