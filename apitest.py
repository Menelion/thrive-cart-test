import requests

BASE_URL = "http://localhost"

user1 = {
    "firstName": "John",
    "lastName": "Doe",
    "email": "john.doe@example.com"
}
user2 = {
    "firstName": "Mary Jane",
    "lastName": "Roe",
    "email": "maryjane.roe@example.com"
}
charge1 = {
    "energy": 7500,
    "cost": 3000,
    "isSuccessful": True
}
charge2 = {
    "energy": 10000,
    "cost": 4000,
    "isSuccessful": False
}
charge3 = {
    "energy": 5000,
    "cost": 2000,
    "isSuccessful": True
}

headers = {
    "Content-Type": "application/json"
}

r1 = requests.post(f"{BASE_URL}/user", json=user1, headers=headers)
r2 = requests.post(f"{BASE_URL}/user", json=user2, headers=headers)
r3 = requests.post(f"{BASE_URL}/user/1/charge", json=charge1, headers=headers)
r4 = requests.post(f"{BASE_URL}/user/1/charge", json=charge2, headers=headers)
r5 = requests.post(f"{BASE_URL}/user/1/charge", json=charge3, headers=headers)
r6 = requests.get(f"{BASE_URL}/user/1/statistics")
r7 = requests.delete(f"{BASE_URL}/user/2")

print("Create a user. Status Code:", r1.status_code)
print("Response Body:", r1.text)
print("Create another user. Status Code:", r2.status_code)
print("Response Body:", r2.text)
print("Create a successful charge for user 1. Status Code:", r3.status_code)
print("Response Body:", r3.text)
print("Create a failed charge for user 1. Status Code:", r4.status_code)
print("Response Body:", r4.text)
print("Create another successful charge for user 1. Status Code:", r5.status_code)
print("Response Body:", r5.text)
print("Get statistics for user 1. Status Code:", r6.status_code)
print("Response Body:", r6.text)
print("Delete user 2. Status Code:", r7.status_code)
print("Response Body:", r7.text)
