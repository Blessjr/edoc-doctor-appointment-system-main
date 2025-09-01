class EDOCChatBot {
  constructor() {
    this.apiEndpoint = "../chatbot/api/chat.php"
    this.isOpen = false
    this.isTyping = false
    this.init()
  }

  init() {
    this.bindEvents()
    this.addWelcomeMessage()
  }

  bindEvents() {
    const toggle = document.getElementById("chat-toggle")
    const close = document.getElementById("chat-close")
    const send = document.getElementById("chat-send")
    const input = document.getElementById("chat-input")

    if (toggle) toggle.addEventListener("click", () => this.toggleChat())
    if (close) close.addEventListener("click", () => this.closeChat())
    if (send) send.addEventListener("click", () => this.sendMessage())

    if (input) {
      input.addEventListener("keypress", (e) => {
        if (e.key === "Enter" && !this.isTyping) {
          this.sendMessage()
        }
      })
    }
  }

  toggleChat() {
    const widget = document.getElementById("medical-chat-widget")
    const toggle = document.getElementById("chat-toggle")

    if (this.isOpen) {
      widget.classList.add("hidden")
      toggle.textContent = "💬 Need Help?"
      this.isOpen = false
    } else {
      widget.classList.remove("hidden")
      toggle.textContent = "💬 Chat Open"
      this.isOpen = true
      this.focusInput()
    }
  }

  closeChat() {
    const widget = document.getElementById("medical-chat-widget")
    const toggle = document.getElementById("chat-toggle")

    widget.classList.add("hidden")
    toggle.textContent = "💬 Need Help?"
    this.isOpen = false
  }

  focusInput() {
    setTimeout(() => {
      const input = document.getElementById("chat-input")
      if (input) input.focus()
    }, 100)
  }

  async sendMessage() {
    const input = document.getElementById("chat-input")
    const sendBtn = document.getElementById("chat-send")
    const message = input.value.trim()

    if (!message || this.isTyping) return

    // Add user message
    this.addMessage(message, "user")
    input.value = ""

    // Disable input while processing
    this.setInputState(false)
    this.showTyping()

    try {
      const response = await fetch(this.apiEndpoint, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ message: message }),
      })

      const data = await response.json()

      this.hideTyping()

      if (data.success) {
        this.addMessage(data.response, "ai")

        // Add role-specific badge if available
        if (data.user_role && data.user_role !== "guest") {
          this.addRoleBadge(data.user_role)
        }
      } else {
        this.addMessage("Sorry, there was an error: " + (data.error || "Unknown error"), "ai error")
      }
    } catch (error) {
      this.hideTyping()
      this.addMessage("Sorry, I'm having trouble connecting. Please try again later.", "ai error")
    } finally {
      this.setInputState(true)
    }
  }

  addMessage(text, sender) {
    const messagesContainer = document.getElementById("chat-messages")
    const messageDiv = document.createElement("div")
    messageDiv.className = `message ${sender}-message`
    messageDiv.textContent = text

    messagesContainer.appendChild(messageDiv)
    this.scrollToBottom()
  }

  addRoleBadge(role) {
    const messagesContainer = document.getElementById("chat-messages")
    const lastMessage = messagesContainer.lastElementChild

    if (lastMessage && lastMessage.classList.contains("ai-message")) {
      const badge = document.createElement("div")
      badge.className = "medical-badge"

      const roleIcons = {
        doctor: "👨‍⚕️ Doctor Mode",
        patient: "🏥 Patient Support",
        admin: "⚙️ Admin Assistant",
        nurse: "👩‍⚕️ Nurse Support",
      }

      badge.innerHTML = roleIcons[role] || "🏥 Medical Assistant"
      lastMessage.appendChild(badge)
    }
  }

  showTyping() {
    this.isTyping = true
    const messagesContainer = document.getElementById("chat-messages")
    const typingDiv = document.createElement("div")
    typingDiv.id = "typing-indicator"
    typingDiv.className = "message ai-message typing"
    typingDiv.innerHTML = 'EDOC Assistant is typing<span class="dots">...</span>'

    messagesContainer.appendChild(typingDiv)
    this.scrollToBottom()
  }

  hideTyping() {
    this.isTyping = false
    const typing = document.getElementById("typing-indicator")
    if (typing) {
      typing.remove()
    }
  }

  setInputState(enabled) {
    const input = document.getElementById("chat-input")
    const sendBtn = document.getElementById("chat-send")

    if (input) input.disabled = !enabled
    if (sendBtn) {
      sendBtn.disabled = !enabled
      sendBtn.textContent = enabled ? "Send" : "..."
    }
  }

  scrollToBottom() {
    const messagesContainer = document.getElementById("chat-messages")
    messagesContainer.scrollTop = messagesContainer.scrollHeight
  }

  addWelcomeMessage() {
    // Add any additional welcome logic here if needed
  }
}

// Initialize EDOC ChatBot when page loads
document.addEventListener("DOMContentLoaded", () => {
  new EDOCChatBot()
})

// Handle page visibility changes
document.addEventListener("visibilitychange", () => {
  if (document.visibilityState === "visible") {
    // Refresh chat state if needed
  }
})
