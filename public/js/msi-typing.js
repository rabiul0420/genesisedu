msiTyping = (id, speed = 300, multi = 0) => {
  var selector = document.querySelector(id);
  var txt = selector.innerText;
  selector.innerHTML = "";
  var i = 0;
  (function msi() {
    if (i < txt.length) {
      if (txt.charAt(i)=="|" && multi == 1) {
        selector.innerHTML = "";
      } else {
        selector.innerHTML += txt.charAt(i);
      }
      i++;
      setTimeout(msi, speed);
    } else {
      i = 0;
      selector.innerHTML = "";
      setTimeout(msi, speed);
    }
  })();
}
