from random import randint, choice
from entities.document import Document
from entities.processing_result import ProcessingResult
from entities.item import Item

def get_cipher_key_match(cipher_doc: Document, keys):
    for key in keys:
        key['match_score'] = randint(0, 100)/100
    
    # Sort keys by match score
    keys.sort(key=lambda x: x['match_score'], reverse=True)
    #Select the top 5 keys
    keys = keys[:5]
    
    return keys
    