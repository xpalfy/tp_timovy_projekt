from flask import Flask, request
from flask import jsonify
from modules.classifier import Classifier
from modules.segmentator import Segmentator
from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Allow all origins (for testing)

#/classify endpoint it takes one parameter, the path to the image to classify
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
    


if __name__ == '__main__':
    
    classifier = Classifier(40, 60)
    print("Classifier initialized")
    print(classifier)
    segmentator = Segmentator()
    print("Segmentator initialized")
    print(Segmentator)
    


    app.run(port=5000)

