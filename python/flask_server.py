from flask import Flask, request
from flask import jsonify
from modules.classifier import Classifier
from modules.segmentator import Segmentator
from modules.encoder import Encoder
from flask import Flask, request, jsonify
from flask_cors import CORS
from controller.document_service import DocumentService
from sqlalchemy.exc import SQLAlchemyError
from controller.db_controller import get_db_session, init_db
from validate_jwt import validate_token
import getpass
import os
from urllib.parse import urlparse

app = Flask(__name__)
CORS(app)  

@app.route('/classify', methods=['POST'])
def classify():
    path = request.json['path']
    print(path)
    return jsonify({"classification": classifier.classify(path)})

@app.route('/segmentate_page', methods=['POST'])
def segmentate_page():
    path = request.json['path']
    print(path)
    return jsonify({"polygon": segmentator.segmentate_page(path)})

@app.route('/segmentate_sections', methods=['POST'])
def segmentate_sections():
    path = request.json['path']
    print(path)
    return jsonify({"polygons": segmentator.segmentate_sections(path)})

@app.route('/segmentate_text', methods=['POST'])
def segmentate_text():
    path = request.json['path']
    print(path)
    return jsonify({"polygons": segmentator.segmentate_text(path)})

@app.route('/crop_polygon', methods=['POST'])
def crop_polygon():
    path = request.json['path']
    polygon = request.json['polygon']
    print(path)
    return jsonify({"cropped_image": segmentator.crop_polygon(path, polygon)})

@app.route('/get_example_json', methods=['GET'])
def get_example_json():
    try:
        data = Encoder.get_json()
        return jsonify(data), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/update_document_title', methods=['POST'])
def update_document_title():
    db = next(get_db_session())
    service = DocumentService(db)
    folder = get_folder_from_referer()
    try:
        data = request.get_json()
        if not data:
            return jsonify({'error': 'Invalid input data'}), 400
        validate_token(data.get('token'))
        author_id = data.get('author_id')
        document_id = data.get('document_id')
        document_title = data.get('document_title')

        document = service.get_document_by_id_and_author(document_id, author_id)
        if not document:
            return jsonify({'error': 'Document not found'}), 404

        if document_title:
            if document.title != document_title:
                if service.document_name_exists(document_title, author_id, exclude_id=document_id):
                    return jsonify({'error': 'Document name already exists'}), 400
                service.update_document_title(document, document_title, author_id, folder)

        service.save_changes()
        print(f"Document {document_id} updated successfully")

        return jsonify({'success': True}), 200

    except SQLAlchemyError as e:
        db.rollback()
        return jsonify({'error': str(e)}), 500
    except Exception as e:
        return jsonify({'error': str(e)}), 400

@app.route('/update_doc_public', methods=['POST'])
def update_doc_public():
    db = next(get_db_session())
    service = DocumentService(db)

    try:
        data = request.get_json()
        if not data:
            return jsonify({'error': 'Invalid input data'}), 400
        validate_token(data.get('token'))
        author_id = data.get('author_id')
        document_id = data.get('document_id')
        is_public = data.get('is_public')

        document = service.get_document_by_id(document_id)
        if not document:
            return jsonify({'error': 'Document not found'}), 404
        if document.author_id != int(author_id):
            return jsonify({'error': 'Unauthorized'}), 403
        if is_public != document.is_public:
            service.edit_public(document, is_public)
            service.save_changes()

        return jsonify({'success': True}), 200

    except SQLAlchemyError as e:
        db.rollback()
        return jsonify({'error': str(e)}), 500
    except Exception as e:
        return jsonify({'error': str(e)}), 400

@app.route('/delete_document', methods=['POST'])
def delete_document():
    db = next(get_db_session())
    service = DocumentService(db)
    folder = get_folder_from_referer()

    try:
        data = request.get_json()
        if not data:
            return jsonify({'error': 'Invalid input data'}), 400
        validate_token(data.get('token'))
        user_id = data.get('id')
        document_id = data.get('document_id')

        if not user_id or not document_id:
            return jsonify({'error': 'Missing required fields'}), 400

        service.delete_document(int(document_id), int(user_id), folder)

        return jsonify({'success': True, 'message': 'Document deleted successfully'}), 200

    except SQLAlchemyError as e:
        db.rollback()
        return jsonify({'error': str(e)}), 500
    except Exception as e:
        return jsonify({'error': str(e)}), 400

@app.route('/add_shared_user', methods=['POST'])
def add_shared_users():
    db = next(get_db_session())
    service = DocumentService(db)

    try:
        data = request.get_json()
        if not data:
            return jsonify({'error': 'Invalid input data'}), 400
        validate_token(data.get('token'))
        document_id = data.get('document_id')
        username = data.get('username')
        
        service.add_shared_user(document_id, username)
        return jsonify({'success': True, 'message': 'Shared users added successfully'}), 200
    except SQLAlchemyError as e:
        db.rollback()
        return jsonify({'error': str(e)}), 500
    except Exception as e:
        return jsonify({'error': str(e)}), 400

@app.route('/remove_shared_user', methods=['POST'])
def remove_shared_users():

    db = next(get_db_session())
    service = DocumentService(db)

    try:
        data = request.get_json()
        if not data:
            return jsonify({'error': 'Invalid input data'}), 400.
        validate_token(data.get('token'))
        document_id = data.get('document_id')
        user_id = data.get('username')
        service.remove_shared_user(document_id, user_id)
        return jsonify({'success': True, 'message': 'Shared users removed successfully'}), 200
    except SQLAlchemyError as e:
        db.rollback()
        return jsonify({'error': str(e)}), 500
    except Exception as e:
        return jsonify({'error': str(e)}), 400

@app.route('/get_key_json', methods=['POST'])
def get_key_json():
    db = next(get_db_session())
    service = DocumentService(db)
    try:
        data = request.get_json()
        if not data:
            return jsonify({'error': 'Invalid input data'}), 400
        validate_token(data.get('token'))
        document_id = data.get('document_id')
        if not document_id:
            return jsonify({'error': 'Document ID is required'}), 400
        result = service.get_key_json(document_id)
        return jsonify(result), 200

    except Exception as e:
        return jsonify({'error': str(e)}), 500

def get_folder_from_referer():
    referer = request.headers.get("Referer")
    if referer:
        path = urlparse(referer).path
        segments = path.strip('/').split('/')
        if segments:
            return segments[0]
    return os.getcwd().split(os.sep)[-1]

if __name__ == '__main__':
    print("Starting Flask server...")
    username = getpass.getuser()
    
    # Initialize the database schema
    try:
        print("Initializing database schema...")
        init_db()
        print("Database schema synchronized successfully.")
    except SQLAlchemyError as e:
        print(f"Error initializing database schema: {e}")
        exit(1)
        
    print(f"Script is run by user: {username}")
    classifier = Classifier(40, 60)
    print("Classifier initialized")
    print(classifier)
    segmentator = Segmentator()
    print("Segmentator initialized")
    print(Segmentator)
    document_service = DocumentService()
    print("DocumentService initialized")
    


    app.run(port=5000)

