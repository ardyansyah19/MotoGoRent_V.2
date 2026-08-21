/* =========================================================
   MotoGoRent — chat.js
   Dipakai bersama oleh customer/chat.php dan admin/chat.php.
   Konfigurasi endpoint & role diset lewat variabel global
   sebelum file ini di-load (lihat inline <script> di masing-masing halaman).
   ========================================================= */
(function () {
  "use strict";

  const endpoint   = window.CHAT_ENDPOINT || "chat_actions.php";
  const sendExtra  = window.CHAT_SEND_EXTRA || {};
  const role       = window.CHAT_ROLE || "customer"; // 'customer' | 'admin'
  const prefill    = window.CHAT_PREFILL || null;

  const messagesEl = document.getElementById("chatMessages");
  const formEl     = document.getElementById("chatForm");
  const inputEl    = document.getElementById("chatInput");

  if (!messagesEl || !formEl || !inputEl) return;

  if (prefill) inputEl.value = prefill;

  let lastCount = 0;

  function fetchUrl() {
    const sep = endpoint.includes("?") ? "&" : "?";
    return `${endpoint}${sep}action=fetch`;
  }

  function renderMessages(messages) {
    messagesEl.innerHTML = "";

    if (messages.length === 0) {
      messagesEl.innerHTML = `<div class="chat-box__empty">Belum ada pesan. Mulai percakapan di bawah ini.</div>`;
      return;
    }

    messages.forEach((m) => {
      const bubble = document.createElement("div");
      const mine = m.sender === role;
      bubble.className = "chat-msg " + (mine ? "chat-msg--me" : "chat-msg--other");

      const time = new Date(m.created_at.replace(" ", "T")).toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
      });

      bubble.innerHTML = `
        <div class="chat-msg__sender">${m.sender === "admin" ? "Admin" : "Kamu"}</div>
        <div class="chat-msg__text"></div>
        <div class="chat-msg__time">${time}</div>
      `;
      bubble.querySelector(".chat-msg__text").textContent = m.pesan;
      messagesEl.appendChild(bubble);
    });

    messagesEl.scrollTop = messagesEl.scrollHeight;
  }

  async function loadMessages() {
    try {
      const res = await fetch(fetchUrl(), { credentials: "same-origin" });
      const data = await res.json();
      if (data.ok) {
        if (data.messages.length !== lastCount) {
          lastCount = data.messages.length;
          renderMessages(data.messages);
        }
      }
    } catch (err) {
      // diamkan error polling agar tidak mengganggu UI
      console.error("Gagal memuat pesan:", err);
    }
  }

  async function sendMessage(pesan) {
    const body = new URLSearchParams({ action: "send", pesan, ...sendExtra });

    const res = await fetch(endpoint, {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body.toString(),
    });
    return res.json();
  }

  formEl.addEventListener("submit", async (e) => {
    e.preventDefault();
    const pesan = inputEl.value.trim();
    if (!pesan) return;

    inputEl.value = "";
    inputEl.disabled = true;

    const result = await sendMessage(pesan);
    inputEl.disabled = false;
    inputEl.focus();

    if (result.ok) {
      await loadMessages();
    } else {
      alert(result.error || "Gagal mengirim pesan.");
    }
  });

  loadMessages();
  setInterval(loadMessages, 3000);
})();
