import requests
import json

# Define URL
url = "https://api.mercadolibre.com/oauth/token"

# Function to renew token
def renew_token(last_refresh_token):
    data = {
        "grant_type": "refresh_token",
        "client_id": "5829758725953784",
        "client_secret": "k2fLgpMWljTTJoHSQs9eMeg1lTgm1JOq",
        "refresh_token": last_refresh_token
    }

    # Send the POST request with JSON data
    response = requests.post(url, data=data)
    
    if response.status_code == 200:
        token_info = response.json()
        access_token = token_info['access_token']
        refresh_token = token_info['refresh_token']

        # Save the new access_token and refresh_token to a file
        with open('tokens.json', 'w') as f:
            json.dump(token_info, f)
        
        # print("Access Token:", access_token)
        # print("Refresh Token:", refresh_token)
        
        return access_token, refresh_token
    else:
        print("Failed to renew token:", response.status_code, response.text)
        return None, None

# Function to read the tokens from file
def get_tokens():
    try:
        with open('tokens.json', 'r') as f:
            token_info = json.load(f)
            return token_info['access_token'], token_info['refresh_token']
    except (FileNotFoundError, KeyError):
        return renew_token("TG-6660b78d4ec9f800013c51a1-1204465713")  # Initial refresh token
# Get the access token and refresh token
access_token, refresh_token = get_tokens()

# If tokens are obtained successfully, ensure future renewals use the latest refresh token
if access_token and refresh_token:
    access_token, refresh_token = renew_token(refresh_token)

# Print the access token and refresh token
print("Access Token:", access_token)
print("Refresh Token:", refresh_token)
