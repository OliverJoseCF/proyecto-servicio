document.addEventListener("DOMContentLoaded", () => {
  const logoInput      = document.getElementById("logo");
  const logoPreview    = document.getElementById("logoPreview");
  const logoName       = document.getElementById("logoName");
  const step1          = document.getElementById("step1");
  const step2          = document.getElementById("step2");
  const step1Indicator = document.getElementById("step1Indicator");
  const step2Indicator = document.getElementById("step2Indicator");
  const continueBtn    = document.getElementById("continueBtn");
  const backBtn        = document.getElementById("backBtn");

  // Vista previa del logo — usando createElement para evitar innerHTML con datos externos
  logoInput.addEventListener("change", (e) => {
    if (e.target.files.length > 0) {
      const file   = e.target.files[0];
      logoName.textContent = file.name;

      const reader = new FileReader();
      reader.onload = (ev) => {
        const img    = document.createElement("img");
        img.src      = ev.target.result;
        img.className = "w-full h-full object-contain";
        img.alt       = "Vista previa del logo";

        logoPreview.innerHTML = "";
        logoPreview.appendChild(img);
        logoPreview.style.display = "flex";
      };
      reader.readAsDataURL(file);
    }
  });

  // Validación y avance al Paso 2
  continueBtn.addEventListener("click", () => {
    const step1Fields = document.querySelectorAll("#step1 input[required], #step1 select[required]");
    let isValid = true;

    step1Fields.forEach((field) => {
      if (!field.value.trim()) {
        isValid = false;
        field.classList.add("border-red-500");
        field.setAttribute("aria-invalid", "true");
      } else {
        field.classList.remove("border-red-500");
        field.removeAttribute("aria-invalid");
      }
    });

    if (isValid) {
      step1.classList.add("hidden");
      step2.classList.remove("hidden");
      step2.classList.add("step-transition");
      step1Indicator.classList.remove("active");
      step1Indicator.classList.add("completed");
      step2Indicator.classList.add("active");
      // Mover foco al primer campo del paso 2 para accesibilidad
      const firstStep2Field = step2.querySelector("input, select");
      if (firstStep2Field) firstStep2Field.focus();
    }
  });

  backBtn.addEventListener("click", () => {
    step2.classList.add("hidden");
    step1.classList.remove("hidden");
    step1.classList.add("step-transition");
    step2Indicator.classList.remove("active");
    step1Indicator.classList.remove("completed");
    step1Indicator.classList.add("active");
  });
});
