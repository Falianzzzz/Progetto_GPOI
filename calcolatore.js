document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('calcForm');
  var risultati = document.getElementById('risultati');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    
    var eta = parseInt(document.getElementById('eta').value, 10);
    var sesso = document.getElementById('sesso').value;
    var peso = parseFloat(document.getElementById('peso').value);
    var altezza = parseFloat(document.getElementById('altezza').value);
    var attivita = parseFloat(document.getElementById('attivita').value);

    if (!eta || !sesso || !peso || !altezza || !attivita) return;

    var bmr;
    if (sesso === 'maschio') {
      bmr = 10 * peso + 6.25 * altezza - 5 * eta + 5;
    } else {
      bmr = 10 * peso + 6.25 * altezza - 5 * eta - 161;
    }

    var tdee = bmr * attivita;

    var protG = 2 * peso;
    var fatG = 1 * peso;
    var restKcal = tdee - (protG * 4 + fatG * 9);
    var carbsG = Math.max(0, restKcal / 4);

    var calTotali = protG * 4 + fatG * 9 + carbsG * 4;
    var protPerc = calTotali > 0 ? (protG * 4 / calTotali * 100) : 0;
    var fatPerc = calTotali > 0 ? (fatG * 9 / calTotali * 100) : 0;
    var carbsPerc = calTotali > 0 ? (carbsG * 4 / calTotali * 100) : 0;

    animateValue('bmrVal', Math.round(bmr));
    animateValue('tdeeVal', Math.round(tdee));
    animateValue('proVal', Math.round(protG) + ' g');
    animateValue('fatVal', Math.round(fatG) + ' g');
    animateValue('carbsVal', Math.round(carbsG) + ' g');

    document.getElementById('proFill').style.width = protPerc + '%';
    document.getElementById('fatFill').style.width = fatPerc + '%';
    document.getElementById('carbsFill').style.width = carbsPerc + '%';

    risultati.classList.add('visible');
    risultati.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  function animateValue(id, final) {
    var el = document.getElementById(id);
    if (typeof final === 'string') {
      el.textContent = final;
      return;
    }
    var start = 0;
    var duration = 600;
    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var current = Math.round(progress * final);
      el.textContent = current;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }
});
