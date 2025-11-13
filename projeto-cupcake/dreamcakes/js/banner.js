const imagensBanner = [
    "../images/cup1.jpg",
    "../images/cup2.png",
    "../images/cup3.jpg",
    "../images/cup4.jpg"
];

let bannerIndex = 0;
const bannerImg = document.getElementById("banner-img");

// Garante que a imagem inicial seja a primeira do array
bannerImg.src = imagensBanner[bannerIndex];

setInterval(() => {
    bannerIndex = (bannerIndex + 1) % imagensBanner.length;
    bannerImg.src = imagensBanner[bannerIndex];
}, 3000);