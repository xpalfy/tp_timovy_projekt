from flask import Flask, request
from flask import jsonify
from modules.classifier import Classifier
from modules.segmentator import Segmentator
from modules.encoder import Encoder
from flask import Flask, request, jsonify
from flask_cors import CORS
from controller.document_service import DocumentService
from sqlalchemy.exc import SQLAlchemyError
from controller.db_controller import get_db_session
import os

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

@app.route('/update_document', methods=['POST'])
def update_document():
    db = next(get_db_session())
    service = DocumentService(db)

    try:
        picture_id = int(request.form.get('id'))
        creator_id = int(request.form.get('user'))
        picture_name = request.form.get('name')
        shared_users_raw = request.form.get('sharedUsers', '')
        shared_users = [u.strip() for u in shared_users_raw.split(',') if u.strip()]

        document = service.get_document_by_id_and_author(picture_id, creator_id)
        if not document:
            return jsonify({'error': 'Document not found'}), 404

        if picture_name:
            if service.document_name_exists(picture_name, creator_id, exclude_id=picture_id):
                return jsonify({'error': 'Document name already exists'}), 400

            if document.title != picture_name:
                service.update_document_title(document, picture_name, creator_id)
                print(f"Document title updated to {picture_name}")

        if shared_users:
            service.update_shared_users(document, shared_users)

        service.save_changes()
        print(f"Document {picture_id} updated successfully")

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
    breakpoint()

    try:
        data = request.get_json()
        if not data:
            return jsonify({'error': 'Invalid input data'}), 400

        user_id = data.get('id')
        doc_id = data.get('doc_id')

        if not user_id or not doc_id:
            return jsonify({'error': 'Missing required fields'}), 400

        service.delete_document(int(doc_id), int(user_id))

        return jsonify({'success': True, 'message': 'Document deleted successfully'}), 200

    except SQLAlchemyError as e:
        db.rollback()
        return jsonify({'error': str(e)}), 500
    except Exception as e:
        return jsonify({'error': str(e)}), 400

if __name__ == '__main__':

    username = os.getlogin()
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

