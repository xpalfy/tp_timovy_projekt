# database.py

from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from entities.base import Base  # assuming your Base is defined in entities/base.py

# Database credentials (same as your config.php)
db_username = "root"
db_password = "NemTudokJobbJelszavat123."
db_host = "localhost"
db_name = "TP_timovy_projekt"

# Construct the DATABASE_URL
DATABASE_URL = f"mysql+pymysql://{db_username}:{db_password}@{db_host}/{db_name}"

# Create the SQLAlchemy engine
engine = create_engine(DATABASE_URL, echo=True)  # echo=True if you want to see SQL logs

# Create session factory
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

# Initialize all tables
def init_db():
    Base.metadata.create_all(bind=engine)

# Dependency to get a database session
def get_db_session():
    """Returns a new SQLAlchemy session."""
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
