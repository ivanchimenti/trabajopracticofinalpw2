function startTimer(duration, display) {
  let timer = duration;
  let minutes, seconds;
  let interval = setInterval(function () {
    minutes = parseInt(timer / 60, 10);
    seconds = parseInt(timer % 60, 10);

    minutes = minutes < 10 ? "0" + minutes : minutes;
    seconds = seconds < 10 ? "0" + seconds : seconds;

    if (display) {
      display.textContent = minutes + ":" + seconds;
    }

    if (timer > 0) {
      timer--;
    } else {
      clearInterval(interval);
      if (!display.classList.contains("timer-ended")) {
        display.classList.add("timer-ended");
        window.location.href = "/partida/end";
      }
    }
  }, 1000);
}

window.onload = function () {
  let tiempoEnvio = parseInt(document.getElementById("tiempoEnvio").value);
  let tiempoActual = Math.floor(Date.now() / 1000);
  let duration = 30 - (tiempoActual - tiempoEnvio);
  let display = document.querySelector("#time");

  startTimer(duration, display);
};
