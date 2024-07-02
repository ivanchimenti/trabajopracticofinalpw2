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
    }
  }, 1000);
}

window.onload = function () {
  var duration = 30; // Duración en segundos
  var display = document.querySelector("#time");

  startTimer(duration, display);
};
