const prevBtns = document.querySelector(".cta-back")
const nextBtns = document.querySelector(".cta-next")
const progress = document.getElementById("progres")
const formSteps = document.querySelectorAll(".form-step")
const progressSteps = document.querySelectorAll(".progress-step")
const progressBar = document.querySelector(".progressbar")
const headingForm = document.querySelector(".heading-form")

let formStepsNum = 0;



nextBtns.addEventListener("click", (event) => {
    // Trouver la question active actuelle
    const activeFormStep = document.querySelector(".form-step-active");
    const selectedInput = activeFormStep.querySelector('input[type="radio"]:checked');

    

    // Supprimer tout message d'erreur existant
    let errorMessage = activeFormStep.querySelector(".error-message");
    if (errorMessage) {
        errorMessage.remove();
    }

    // Vérification si aucune option n'est sélectionnée
    if (!selectedInput) {
        // Ajouter un message d'erreur
        errorMessage = document.createElement("div");
        errorMessage.classList.add("error-message");
        errorMessage.textContent = "Veuillez sélectionner une réponse pour continuer.";
        activeFormStep.appendChild(errorMessage);
        event.preventDefault(); // Bloque l'avancement
        return;
    }

    // Si une option est sélectionnée, passe à l'étape suivante
    formStepsNum++;
    updateFormSteps();
    updateProgressbar();
    updateImg();

});

prevBtns.addEventListener("click", () => {
    formStepsNum--;
    updateFormSteps();
    updateProgressbar();
    updateImg();

})

function updateFormSteps(){

    formSteps.forEach( formStep => {
        formStep.classList.contains("form-step-active") && 
        formStep.classList.add("d-none")
        prevBtns.classList.remove("disabled")
        nextBtns.classList.remove("d-none")
    })
    prevBtns.classList.toggle("disabled", formStepsNum === 0);
    nextBtns.classList.toggle("d-none", formStepsNum === formSteps.length - 1);
    prevBtns.classList.toggle("d-none", formStepsNum === formSteps.length - 1);
    progressBar.classList.toggle("d-none", formStepsNum === formSteps.length - 1);
    headingForm.classList.toggle("d-none", formStepsNum === formSteps.length - 1);
    

    formSteps[formStepsNum].classList.add("form-step-active")
    if (formStepsNum >= 1){
        formSteps[formStepsNum - 1].classList.remove("form-step-active");   
    }
    formSteps[formStepsNum].classList.remove("d-none")
    // if (formStepsNum === formSteps.length - 1){
    //     prevBtns.classList.add("d-none")   
    // }
}

function updateProgressbar(){
    progressSteps.forEach((progressStep, idx) => {
        if(idx < formStepsNum +1){
            progressStep.classList.add("progress-step-active")
        } else {
            progressStep.classList.remove("progress-step-active")
        }
    })
    const progressActive = document.querySelectorAll(".progress-step-active");
    progress.style.width = ((progressActive.length -1) / (progressSteps.length -1)) * 100 + "%";   
    
}

function updateImg() {
    const illustration = document.getElementById('illustration')
    illustration.innerHTML = "";
    const images = [
        `<div class="illustration-img" style="background-image: url('img/type.jpg');"></div>`,
        `<div class="illustration-img" style="background-image: url('img/kilometres.jpg');"></div>`,
        `<div class="illustration-img" style="background-image: url('img/kmannuel.jpg');"></div>`,
        `<div class="illustration-img" style="background-image: url('img/longtravel.jpg');"></div>`,
        `<div class="illustration-img" style="background-image: url('img/borne.jpg');"></div>`,
        `<div class="illustration-img" style="background-image: url('img/budget.jpg');"></div>`,
        `<div class="illustration-img" style="background-image: url('img/nom.jpg');"></div>`,
        `<div class="illustration-img" style="background-image: url('img/result.jpg');"></div>`,
    ]
    illustration.innerHTML += images[formStepsNum]
        
}


function adjustIllustrationHeight() {
    const form = document.getElementById("form");
    const illustration = document.getElementById("illustration");

    if (form && illustration) {
        illustration.style.height = form.offsetHeight + "px";
    }
}

// Exécuter au chargement et surveiller les changements de hauteur
window.addEventListener("load", adjustIllustrationHeight);
window.addEventListener("resize", adjustIllustrationHeight);
new ResizeObserver(adjustIllustrationHeight).observe(document.getElementById("form"));