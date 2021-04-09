let check = function () {
    if (document.getElementById('password').value ===
        document.getElementById('confirm-password').value) {
        document.getElementById('message').style.color = 'green';
        document.getElementById('message').innerHTML = 'Passwords match';
        document.getElementById('submit-passwords').disabled = false;
    } else {
        document.getElementById('message').style.color = 'red';
        document.getElementById('message').innerHTML = 'Passwords do not match';
        document.getElementById('submit-passwords').disabled = true;
    }
}
