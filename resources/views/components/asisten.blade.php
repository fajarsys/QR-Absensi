<div class="card border-0 shadow-sm" style="border-radius: 15px; height: 450px; display: flex; flex-direction: column;">
    <div class="card-header bg-primary text-white py-3 d-flex align-items-center" style="border-radius: 15px 15px 0 0;">
        <i class="bi bi-robot fs-5 me-2"></i>
        <h6 class="mb-0 fw-bold">AI Assistant - Evaluasi</h6>
    </div>
    
    <div class="card-body p-3" id="chat-box" style="overflow-y: auto; flex-grow: 1; background-color: #f8f9fa;">
        <div class="d-flex justify-content-start mb-3">
            <div class="bg-white border rounded p-3 shadow-sm" style="max-width: 85%; font-size: 14px;">
                <p class="mb-0 text-dark">Sistem AI siap. Ketikkan data karyawan yang ingin dievaluasi.</p>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white py-2 border-top" style="border-radius: 0 0 15px 15px;">
        <form id="chat-form" class="d-flex gap-2 align-items-end" data-action="{{ route('admin.chat.send') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            
            <div style="flex-grow: 1;">
                <textarea id="message-input" class="form-control bg-light border-0" rows="1" placeholder="Ketik prompt..." required style="resize: none; border-radius: 10px; padding: 10px; font-size: 14px;"></textarea>
            </div>
            <button type="submit" id="btn-send" class="btn btn-primary px-3 py-2" style="border-radius: 10px;">
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const chatBox = document.getElementById('chat-box');
    const btnSend = document.getElementById('btn-send');
    
    // Mengambil URL dari data-action form
    const actionUrl = chatForm.getAttribute('data-action');
    
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        if(this.scrollHeight > 100) this.style.overflowY = 'auto';
    });

    messageInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        
        // Langsung klik tombol send secara programmatik
        btnSend.click(); 
    }
    });

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        appendMessage(message, 'user');
        messageInput.value = '';
        messageInput.style.height = 'auto';
        
        const typingIndicatorId = showTypingIndicator();
        btnSend.disabled = true;
        messageInput.disabled = true;

        try {
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Membaca value dari input hidden CSRF
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });

            const result = await response.json();
            removeElement(typingIndicatorId);

            if (result.status === 'success') {
                appendMessage(result.data, 'ai');
            } else {
                appendMessage('Error: ' + result.message, 'error');
            }
        } catch (error) {
            removeElement(typingIndicatorId);
            appendMessage('Error: Gagal menghubungi server. Periksa Inspect Element -> Network', 'error');
            console.error(error);
        } finally {
            btnSend.disabled = false;
            messageInput.disabled = false;
            messageInput.focus();
        }
    });

    function appendMessage(text, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('d-flex', 'mb-3');
    let bubbleHtml = '';
    
        if (sender === 'user') {
            messageDiv.classList.add('justify-content-end');
            // Pesan user tetap di-escape agar aman dari XSS
            bubbleHtml = `
                <div class="bg-primary text-white border-0 rounded p-2 shadow-sm" 
                    style="max-width: 85%; font-size: 14px; border-bottom-right-radius: 0 !important; white-space: pre-wrap;">
                    ${escapeHtml(text)}
                </div>`;
        } else if (sender === 'ai') {
            messageDiv.classList.add('justify-content-start');
            bubbleHtml = `
                <div class="bg-white text-dark border rounded p-2 shadow-sm" 
                    style="max-width: 70%; font-size: 14px; border-bottom-left-radius: 0 !important;">
                    ${text}
                </div>`;
        } else {
            messageDiv.classList.add('justify-content-center');
            bubbleHtml = `
                <div class="bg-danger text-white rounded p-2 text-center" style="font-size: 13px;">
                    ${escapeHtml(text)}
                </div>`;
        }

        messageDiv.innerHTML = bubbleHtml;
        chatBox.appendChild(messageDiv);
        
        // Pastikan scroll otomatis ke bawah setelah pesan muncul
        setTimeout(() => {
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 50);
    }

    function showTypingIndicator() {
        const id = 'typing-' + Date.now();
        const typingDiv = document.createElement('div');
        typingDiv.id = id;
        typingDiv.classList.add('d-flex', 'justify-content-start', 'mb-3');
        typingDiv.innerHTML = `
            <div class="bg-white border rounded p-2 shadow-sm d-flex align-items-center gap-1" style="border-bottom-left-radius: 0 !important;">
                <span class="spinner-grow spinner-grow-sm text-secondary" style="width: 0.4rem; height: 0.4rem;" role="status"></span>
                <span class="spinner-grow spinner-grow-sm text-secondary" style="width: 0.4rem; height: 0.4rem;" role="status"></span>
                <span class="spinner-grow spinner-grow-sm text-secondary" style="width: 0.4rem; height: 0.4rem;" role="status"></span>
            </div>
        `;
        chatBox.appendChild(typingDiv);
        chatBox.scrollTop = chatBox.scrollHeight;
        return id;
    }

    function removeElement(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function escapeHtml(unsafe) {
        return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }
});
</script>