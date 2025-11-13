window.onload = function() {
    setTimeout(() => {
    document.getElementById("welcomePopup").classList.add("active");
    }, 1000);
}

function closePopup() {
    document.getElementById("welcomePopup").classList.remove("active");
}