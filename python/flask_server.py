from flask import Flask, request
from flask import jsonify
from modules.classifier import Classifier

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


if __name__ == '__main__':
    
    classifier = Classifier(40, 60)
    print("Classifier initialized")
    print(classifier)


    app.run(port=5000)

