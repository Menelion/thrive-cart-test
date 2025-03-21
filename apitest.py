import requests

session = requests.Session()  # Maintain session for persistence

BASE_URL = "http://localhost"

# Add products
r1 = session.post(f"{BASE_URL}/basket/add/R01")
print(r1.text())
r2 = session.post(f"{BASE_URL}/basket/add/G01")
print(r2.text())

# Get total
r3 = session.get(f"{BASE_URL}/basket/total")
print(r3.text())
