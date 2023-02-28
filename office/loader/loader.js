function showLoader(){
    const loader = document.querySelector('.loader-wrapper');
    loader.classList.add('show');
}

function hideLoader(){
    const loader = document.querySelector('.loader-wrapper');
    loader.classList.remove('show');
}