const openingScreen = document.getElementById("opening-screen");
const openInvitationButton = document.getElementById("open-invitation");
const guestName = document.getElementById("guest-name");
const form = document.getElementById("rsvp-form");
const formNote = document.getElementById("form-note");

document.body.classList.add("is-locked");

const query = new URLSearchParams(window.location.search);
const guest = query.get("to");

if (guest) {
  guestName.textContent = guest;
}

const eventDate = new Date("2026-09-28T09:00:00+07:00").getTime();
const countdownEls = {
  days: document.getElementById("days"),
  hours: document.getElementById("hours"),
  minutes: document.getElementById("minutes"),
  seconds: document.getElementById("seconds"),
};

function pad(value) {
  return String(value).padStart(2, "0");
}

function updateCountdown() {
  const now = Date.now();
  const distance = Math.max(eventDate - now, 0);

  const days = Math.floor(distance / (1000 * 60 * 60 * 24));
  const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
  const minutes = Math.floor((distance / (1000 * 60)) % 60);
  const seconds = Math.floor((distance / 1000) % 60);

  countdownEls.days.textContent = pad(days);
  countdownEls.hours.textContent = pad(hours);
  countdownEls.minutes.textContent = pad(minutes);
  countdownEls.seconds.textContent = pad(seconds);
}

function openInvitation() {
  openingScreen.classList.add("is-hidden");
  openingScreen.setAttribute("aria-hidden", "true");
  document.body.classList.remove("is-locked");
}

openInvitationButton.addEventListener("click", openInvitation);

form.addEventListener("submit", (event) => {
  event.preventDefault();

  const data = new FormData(form);
  const name = data.get("name");
  const attendance = data.get("attendance");
  const attendanceLabel = {
    yes: "will attend",
    maybe: "is still confirming",
    no: "is unable to attend",
  }[attendance] || attendance;

  formNote.textContent = `Thank you, ${name}. Your RSVP indicates ${attendanceLabel}. Connect this form to your preferred backend for production use.`;
  form.reset();
});

updateCountdown();
setInterval(updateCountdown, 1000);
