document.addEventListener("DOMContentLoaded", function () {
  const notice = document.getElementById("atweaks-admin-notice");
  if (!notice) return;

  // Check if the user has already dismissed the notice
  if (sessionStorage.getItem("atweaksAdminNoticeDismissed") === "true") {
    notice.classList.add("atweaks-hidden");
  } else {
    notice.classList.remove("atweaks-hidden");
  }

  // Wait a moment before adding the click event listener
  setTimeout(() => {
    const dismissButton = notice.querySelector(".notice-dismiss");
    if (dismissButton) {
      dismissButton.addEventListener("click", () => {
        sessionStorage.setItem("atweaksAdminNoticeDismissed", "true");
        notice.classList.add("atweaks-hidden");
      });
    }
  }, 100);
});
