import re
from flask import session, redirect, make_response
import jwt
from jwt import InvalidTokenError


def get_jwt_secret_from_php(path='jwt.php'):
    with open(path, 'r') as f:
        content = f.read()

    match = re.search(r"'secret'\s*=>\s*'([^']+)'", content)
    if match:
        return match.group(1)
    else:
        raise ValueError("Secret not found in jwt.php")

def validate_token(token=None):

    if not token:
        session['toast'] = {'type': 'error', 'message': 'Unauthorized: Token not found'}
        response = make_response(redirect('/login'))
        response.status_code = 401
        return response

    secret = get_jwt_secret_from_php()
    decoded = jwt.decode(token, secret, algorithms=['HS256'], issuer='https://test.tptimovyprojekt.software/tp_timovy_projekt', options={"verify_aud": False})

    if (decoded.get('iss') != 'https://test.tptimovyprojekt.software/tp_timovy_projekt' or
            decoded.get('aud') != 'https://test.tptimovyprojekt.software/tp_timovy_projekt'):
        raise InvalidTokenError('Invalid issuer or audience')

    return decoded.get('data', {})
