from flask import Flask, request
from flask import jsonify
from modules.classifier import Classifier
from modules.segmentator import Segmentator
from modules.encoder import Encoder
from flask import Flask, request, jsonify
from flask_cors import CORS
from controller.document_service import DocumentService
from controller.user_service import UserService
from sqlalchemy.exc import SQLAlchemyError
from controller.db_controller import get_db_session, init_db
from validate_jwt import validate_token
import getpass
import os
from urllib.parse import urlparse
from flasgger import Swagger

app = Flask(__name__)
CORS(app)
swagger = Swagger(app)

@app.route('/modules/classify', methods=['POST'])
def classify():
    """
    Classify a document image.
    ---
    tags:
      - Modules
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required:
            - path
          properties:
            path:
              type: string
              description: Path to the image file
    responses:
      200:
        description: Classification result
        schema:
          type: object
          properties:
            classification:
              type: string
      400:
        description: Invalid input
    """
    path = request.json['path']
    print(path)
    return jsonify({"classification": classifier.classify(path)})

@app.route('/modules/segmentate_page', methods=['POST'])
def segmentate_page():
    """
    Segmentate a page image.
    ---
    tags:
      - Modules
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required:
            - path
          properties:
            path:
              type: string
              description: Path to the image file
    responses:
      200:
        description: Segmentation result
        schema:
          type: object
      400:
        description: Invalid input
    """
    path = request.json['path']
    folder = get_folder_from_referer()
    path = os.path.join('..',folder, path[1:])
    result:dict = segmentator.segmentate_page(path)
    return jsonify(result)

@app.route('/modules/segmentate_sections', methods=['POST'])
def segmentate_sections():
    """
    Segmentate sections in a page image.
    ---
    tags:
      - Modules
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required:
            - path
          properties:
            path:
              type: string
              description: Path to the image file
    responses:
      200:
        description: Segmentation polygons
        schema:
          type: object
          properties:
            polygons:
              type: array
              items:
                type: object
      400:
        description: Invalid input
    """
    path = request.json['path']
    print(path)
    return jsonify({"polygons": segmentator.segmentate_sections(path)})

@app.route('/segmentate_text', methods=['POST'])
def segmentate_text():
    """
    Segmentate text in a page image.
    ---
    tags:
      - Modules
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required:
            - path
          properties:
            path:
              type: string
              description: Path to the image file
    responses:
      200:
        description: Segmentation polygons
        schema:
          type: object
          properties:
            polygons:
              type: array
              items:
                type: object
      400:
        description: Invalid input
    """
    path = request.json['path']
    return jsonify({"polygons": segmentator.segmentate_text(path)})

@app.route('/crop_polygon', methods=['POST'])
def crop_polygon():
    """
    Crop a polygon from an image.
    ---
    tags:
      - Modules
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required:
            - path
            - polygon
          properties:
            path:
              type: string
              description: Path to the image file
            polygon:
              type: object
              description: Polygon coordinates
    responses:
      200:
        description: Cropped image result
        schema:
          type: object
          properties:
            cropped_image:
              type: string
      400:
        description: Invalid input
    """
    path = request.json['path']
    polygon = request.json['polygon']
    print(path)
    return jsonify({"cropped_image": segmentator.crop_polygon(path, polygon)})

@app.route('/modules/get_cipher_json', methods=['POST'])
def get_cipher_json():
    """
    Get cipher JSON data.
    ---
    tags:
      - Modules
    responses:
      200:
        description: Cipher JSON data
        schema:
          type: object
      500:
        description: Server error
    """
    try:
        data = encoder.get_cipher_json()
        return jsonify(data), 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/documents/get_document', methods=['POST'])
def get_document():
    """
    Get a document and its metadata.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, document_id, user_id]
          properties:
            token:
              type: string
            document_id:
              type: integer
            user_id:
              type: integer
    responses:
      200:
        description: Document data
        schema:
          type: object
      400:
        description: Invalid input
      404:
        description: Document not found
    """
    with get_db_session() as db:
        service = DocumentService(db)
        
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            document_id = data.get('document_id')
            user_id = data.get('user_id')

            if not document_id:
                return jsonify({'error': 'Document ID is required'}), 400
            if not user_id:
                return jsonify({'error': 'User ID is required'}), 400

            document = service.get_document_id_and_user_id(document_id, user_id)
            if not document:
                return jsonify({'error': 'Document not found'}), 404
            # Get image paths related to the document
            image_paths = service.get_image_paths_by_document_id(document_id)

            # Get shared users for the document
            shared_users = service.get_shared_users_by_document_id(document_id, user_id)
            
            publish_date = service.get_publish_date_by_document_id(document_id)
            if not publish_date:
                return jsonify({'error': 'Publish date not found'}), 404
    
            return jsonify({
                'id': document.id,
                'title': document.title,
                'author_id': document.author_id,
                'author_name': document.author.username,
                'status': document.status.name,
                'description': document.description,
                'ispublic': document.is_public,
                'imagePaths': image_paths,
                'sharedUsers': shared_users,
                'publish_date': publish_date,
                'itemId': document.items[-1].id,
                'historical_author': document.historical_author,
                'historical_date': document.historical_date,
                'country': document.country,
                'language': document.language 
            }), 200

        except Exception as e:
            return jsonify({'error': str(e)}), 400

@app.route('/documents/update_document_title', methods=['POST'])
def update_document_title():
    """
    Update the title of a document.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, author_id, document_id, new_title]
          properties:
            token:
              type: string
            author_id:
              type: integer
            document_id:
              type: integer
            new_title:
              type: string
    responses:
      200:
        description: Title updated
      400:
        description: Invalid input
      403:
        description: Unauthorized
      404:
        description: Document not found
      500:
        description: Database error
    """
    with get_db_session() as db:
        service = DocumentService(db)
        folder = get_folder_from_referer()
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            author_id = data.get('author_id')
            document_id = data.get('document_id')
            document_title = data.get('new_title')

            document = service.get_document_by_id(document_id)
            if not document:
                return jsonify({'error': 'Document not found'}), 404
            if document.author_id != int(author_id):
                return jsonify({'error': 'Unauthorized'}), 403

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

@app.route('/documents/update_doc_public', methods=['POST'])
def update_doc_public():
    """
    Update the public status of a document.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, author_id, document_id, is_public]
          properties:
            token:
              type: string
            author_id:
              type: integer
            document_id:
              type: integer
            is_public:
              type: boolean
    responses:
      200:
        description: Public status updated
      400:
        description: Invalid input
      403:
        description: Unauthorized
      404:
        description: Document not found
      500:
        description: Database error
    """
    with get_db_session() as db:
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

@app.route('/documents/delete_document', methods=['DELETE'])
def delete_document():
    """
    Delete a document.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, id, doc_id]
          properties:
            token:
              type: string
            id:
              type: integer
            doc_id:
              type: integer
    responses:
      200:
        description: Document deleted
      400:
        description: Invalid input
      500:
        description: Database error
    """
    
    with get_db_session() as db:
        service = DocumentService(db)
        folder = get_folder_from_referer()

        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            user_id = data.get('id')
            document_id = data.get('doc_id')

            if not user_id or not document_id:
                return jsonify({'error': 'Missing required fields'}), 400

            service.delete_document(int(document_id), int(user_id), folder)

            return jsonify({'success': True, 'message': 'Document deleted successfully'}), 200

        except SQLAlchemyError as e:
            db.rollback()
            return jsonify({'error': str(e)}), 500
        except Exception as e:
            return jsonify({'error': str(e)}), 400

@app.route('/documents/add_shared_user', methods=['POST'])
def add_shared_users():
    """
    Add a shared user to a document.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, document_id, username]
          properties:
            token:
              type: string
            document_id:
              type: integer
            username:
              type: string
    responses:
      200:
        description: Shared user added
      400:
        description: Invalid input
      500:
        description: Database error
    """
    
    with get_db_session() as db:
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

@app.route('/documents/remove_shared_user', methods=['POST'])
def remove_shared_users():
    """
    Remove a shared user from a document.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, document_id, username]
          properties:
            token:
              type: string
            document_id:
              type: integer
            username:
              type: string
    responses:
      200:
        description: Shared user removed
      400:
        description: Invalid input
      500:
        description: Database error
    """
    
    with get_db_session() as db:
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

@app.route('/documents/get_json', methods=['POST'])
def get_json_from_db():
    """
    Get JSON data for a document.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, document_id, user_id]
          properties:
            token:
              type: string
            document_id:
              type: integer
            user_id:
              type: integer
    responses:
      200:
        description: JSON data
      400:
        description: Invalid input
      500:
        description: Server error
    """
    
    with get_db_session() as db:
        service = DocumentService(db)
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            document_id = data.get('document_id')
            user_id = data.get('user_id')
            if not document_id:
                return jsonify({'error': 'Document ID is required'}), 400
            if not user_id:
                return jsonify({'error': 'User ID is required'}), 400
            result = service.get_json_from_db(document_id, user_id)
            return jsonify(result), 200

        except Exception as e:
            return jsonify({'error': str(e)}), 500

@app.route('/documents/save_json', methods=['POST'])
def save_json_to_db():
    """
    Save JSON data for a document.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, document_id, user_id, json_data]
          properties:
            token:
              type: string
            document_id:
              type: integer
            user_id:
              type: integer
            json_data:
              type: object
    responses:
      200:
        description: JSON saved
      400:
        description: Invalid input
      500:
        description: Server error
    """
    
    with get_db_session() as db:
        service = DocumentService(db)
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            document_id = data.get('document_id')
            user_id = data.get('user_id')
            json_data = data.get('json_data')
            if not document_id:
                return jsonify({'error': 'Document ID is required'}), 400
            if not user_id:
                return jsonify({'error': 'User ID is required'}), 400
            if not json_data:
                return jsonify({'error': 'JSON data is required'}), 400
            service.save_json_to_db(document_id, user_id, json_data)
            service.save_changes()
            print(f"Document {document_id} updated successfully")
            return jsonify({'success': True}), 200
        except Exception as e:
            return jsonify({'error': str(e)}), 500

@app.route('/documents/save_processing_result', methods=['POST'])
def save_processing_result():
    """
    Save processing result for a document item.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, document_id, item_id, user_id, status]
          properties:
            token:
              type: string
            document_id:
              type: integer
            item_id:
              type: integer
            user_id:
              type: integer
            status:
              type: string
    responses:
      200:
        description: Processing result saved
      400:
        description: Invalid input
      500:
        description: Database error
    """
    with get_db_session() as db:
        service = DocumentService(db)
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            required_fields = ['document_id', 'item_id', 'user_id', 'status']
            if not all(field in data for field in required_fields):
                raise ValueError("Invalid input data")
            service.save_processing_result(data)
        except SQLAlchemyError as e:
            db.rollback()
            return jsonify({'error': str(e)}), 500
        except Exception as e:
            return jsonify({'error': str(e)}), 400
        return jsonify({'success': True}), 200

@app.route('/documents/get_documents_by_user_and_status', methods=['POST'])
def get_documents_by_user_and_status():
    """
    Get documents by user and status.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, user_id, status]
          properties:
            token:
              type: string
            user_id:
              type: integer
            status:
              type: string
    responses:
      200:
        description: List of documents
      400:
        description: Invalid input
      500:
        description: Server error
    """
    with get_db_session() as db:
        service = DocumentService(db)
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            user_id = data.get('user_id')
            status = data.get('status')
            not_public = bool(data.get('not_public'))
            if not user_id:
                return jsonify({'error': 'User ID is required'}), 400
            if not status:
                return jsonify({'error': 'Status is required'}), 400
            documents = service.get_documents_by_user_id_and_status(user_id, status, not_public)
            result = [
                {
                    'id': doc.id,
                    'title': doc.title,
                    'doc_type': doc.doc_type.name
                }
                for doc in documents
            ]
            return jsonify(result), 200
        except Exception as e:
            return jsonify({'error': str(e)}), 500

@app.route('/documents/get_items_by_doc_and_status', methods=['POST'])
def get_items_by_doc_and_status():
    """
    Get items by document and status.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, document_id, user_id, status]
          properties:
            token:
              type: string
            document_id:
              type: integer
            user_id:
              type: integer
            status:
              type: string
    responses:
      200:
        description: List of items
      400:
        description: Invalid input
      500:
        description: Server error
    """
    with get_db_session() as db:
        service = DocumentService(db)
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            document_id = data.get('document_id')
            user_id = data.get('user_id')
            status = data.get('status')
            if not document_id:
                return jsonify({'error': 'Document ID is required'}), 400
            if not status:
                return jsonify({'error': 'Status is required'}), 400
            
            items = service.get_items_by_document_id_and_status(document_id, status, user_id)
            if not items:
                return jsonify([]), 200  # match PHP: return empty array if no results

            # Get doc_type from document to inject into each item
            document = service.get_document_by_id(document_id)
            doc_type = document.doc_type
            result = [
                {
                    'id': item.id,
                    'title': item.title,
                    'image_path': item.image_path,
                    'type': doc_type.name
                }
                for item in items
            ]
            return jsonify(result), 200
        except Exception as e:
            return jsonify({'error': str(e)}), 500

@app.route('/users/delete_user', methods=['DELETE'])
def delete_user():
    """
    Delete a user and all their documents.
    ---
    tags:
      - Users
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, user_id]
          properties:
            token:
              type: string
            user_id:
              type: integer
    responses:
      200:
        description: User deleted
      400:
        description: Invalid input
      500:
        description: Database error
    """
    with get_db_session() as db:
        u_service = UserService(db)
        db_service = DocumentService(db)
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            user_id = data.get('user_id')
            if not user_id:
                return jsonify({'error': 'User ID is required'}), 400
            folder = get_folder_from_referer()
            db_service.delete_user_documents(user_id, folder)
            u_service.delete_user(user_id)
            db.commit()
            return jsonify({'success': True}), 200
        except SQLAlchemyError as e:
            db.rollback()
            return jsonify({'error': str(e)}), 500
        except Exception as e:
            return jsonify({'error': str(e)}), 400

@app.route('/modules/encode_letters', methods=['POST'])
def encode_letters():
    """
    Encode letters for a document.
    ---
    tags:
      - Modules
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, document_id, user_id]
          properties:
            token:
              type: string
            document_id:
              type: integer
            user_id:
              type: integer
    responses:
      200:
        description: Encoded letters
      400:
        description: Invalid input
      500:
        description: Server error
    """
    with get_db_session() as db:
        service = DocumentService(db)
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            document_id = data.get('document_id')
            user_id = data.get('user_id')
            if not document_id:
                return jsonify({'error': 'Document ID is required'}), 400
            if not user_id:
                return jsonify({'error': 'User ID is required'}), 400
            service.encode_letters(document_id, user_id)
            return jsonify({'success': True, 'message': 'Letters encoded successfully'}), 200
        except Exception as e:
            return jsonify({'error': str(e)}), 500

@app.route('/documents/get_processing_result_status', methods=['POST'])
def get_processing_result_status():
    """
    Get processing result status for a document.
    ---
    tags:
      - Documents
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, document_id, user_id]
          properties:
            token:
              type: string
            document_id:
              type: integer
            user_id:
              type: integer
    responses:
      200:
        description: Processing result status
      400:
        description: Invalid input
      500:
        description: Server error
    """
    with get_db_session() as db:
        service = DocumentService(db)
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            document_id = data.get('document_id')
            user_id = data.get('user_id')
            if not document_id:
                return jsonify({'error': 'Document ID is required'}), 400
            if not user_id:
                return jsonify({'error': 'User ID is required'}), 400
            result = service.get_processing_result_status(document_id, user_id)
            return jsonify(result), 200
        except Exception as e:
            return jsonify({'error': str(e)}), 500

@app.route('/modules/get_processing_result_status', methods=['POST'])
def get_keys_for_cipher():
    """
    Get keys for cipher document.
    ---
    tags:
      - Modules
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, document_id, user_id]
          properties:
            token:
              type: string
            document_id:
              type: integer
            user_id:
              type: integer
    responses:
      200:
        description: Keys for cipher
      400:
        description: Invalid input
      500:
        description: Server error
    """
    with get_db_session() as db:
        service = DocumentService(db)
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            document_id = data.get('document_id')
            user_id = data.get('user_id')
            if not document_id:
                return jsonify({'error': 'Document ID is required'}), 400
            if not user_id:
                return jsonify({'error': 'User ID is required'}), 400
            result = service.get_keys_for_cipher(document_id, user_id)
            return jsonify(result), 200
        except Exception as e:
            return jsonify({'error': str(e)}), 500

@app.route('/modules/decrypt_cipher_with_key', methods=['POST'])
def decrypt_cipher_With_key():
    """
    Decrypt a cipher document with a key document.
    ---
    tags:
      - Modules
    parameters:
      - in: body
        name: body
        required: true
        schema:
          type: object
          required: [token, cipher_document_id, key_document_id, user_id]
          properties:
            token:
              type: string
            cipher_document_id:
              type: integer
            key_document_id:
              type: integer
            user_id:
              type: integer
    responses:
      200:
        description: Decryption result
      400:
        description: Invalid input
      500:
        description: Server error
    """
    with get_db_session() as db:
        service = DocumentService(db)
        try:
            data = request.get_json()
            if not data:
                return jsonify({'error': 'Invalid input data'}), 400
            validate_token(data.get('token'))
            cipher_document_id = data.get('cipher_document_id')
            key_document_id = data.get('key_document_id')
            user_id = data.get('user_id')
            if not cipher_document_id:
                return jsonify({'error': 'Cipher Document ID is required'}), 400
            if not key_document_id:
                return jsonify({'error': 'Key Document ID is required'}), 400
            if not user_id:
                return jsonify({'error': 'User ID is required'}), 400
            result = service.decrypt_cipher_with_key(cipher_document_id, key_document_id, user_id)
            return jsonify(result), 200
        except Exception as e:
            return jsonify({'error': str(e)}), 500

def get_folder_from_referer():
    referer = request.headers.get("X-Caller-Url")
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
    classifier = Classifier(70, 90)
    print("Classifier initialized")
    print(classifier)
    segmentator = Segmentator()
    print("Segmentator initialized")
    print(Segmentator)
    document_service = DocumentService()
    print("DocumentService initialized")
    user_service = UserService()
    print("UserService initialized")
    encoder = Encoder()
    print("Encoder initialized")
    


    app.run(port=5000)

