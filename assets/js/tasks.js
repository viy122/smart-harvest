// lightweight tasks loader to avoid 404 and safely call page init if present
(function () {
  function tasksInit() {
    try {
      // if the page defines ensureCalendarInitialized or other init helpers, call them
      if (typeof ensureCalendarInitialized === "function") ensureCalendarInitialized();
      // also try to render calendar if a FullCalendar instance exists
      if (typeof fcCalendar !== "undefined" && fcCalendar && typeof fcCalendar.render === "function") {
        try { fcCalendar.render(); } catch (e) { /* ignore */ }
      }
    } catch (err) {
      console.error("tasksInit error:", err);
    }
  }

  // expose globally so layout.php can call it
  if (typeof window !== "undefined") window.tasksInit = tasksInit;
})();