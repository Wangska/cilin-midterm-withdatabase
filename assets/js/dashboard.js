// Global variables
let currentNoteId = null;
let currentColorNoteId = null;
let currentDeleteNoteId = null;

// DOM elements
const noteModal = document.getElementById('noteModal');
const colorModal = document.getElementById('colorModal');
const deleteModal = document.getElementById('deleteModal');
const noteForm = document.getElementById('noteForm');
const userDropdown = document.getElementById('userDropdown');

// Modal functions
function openNoteModal(noteId = null) {
    const modalTitle = document.getElementById('modalTitle');
    const noteIdInput = document.getElementById('noteId');
    const noteTitleInput = document.getElementById('noteTitle');
    const noteContentInput = document.getElementById('noteContent');
    const noteColorInput = document.getElementById('noteColor');

    if (noteId) {
        // Edit mode
        modalTitle.textContent = 'Edit Note';
        noteIdInput.value = noteId;
        
        // Get note data from the note card
        const noteCard = document.querySelector(`[data-note-id="${noteId}"]`);
        const title = noteCard.querySelector('.note-header h3').textContent;
        const content = noteCard.querySelector('.note-content p').innerHTML.replace(/<br\s*\/?>/gi, '\n');
        const color = rgbToHex(noteCard.style.backgroundColor) || '#ffffff';
        
        noteTitleInput.value = title;
        noteContentInput.value = content;
        noteColorInput.value = color;
    } else {
        // Create mode
        modalTitle.textContent = 'Add New Note';
        noteIdInput.value = '';
        noteTitleInput.value = '';
        noteContentInput.value = '';
        noteColorInput.value = '#ffffff';
    }
    
    noteModal.classList.add('show');
    noteTitleInput.focus();
}

function closeNoteModal() {
    noteModal.classList.remove('show');
    noteForm.reset();
}

function openColorPicker(noteId) {
    currentColorNoteId = noteId;
    colorModal.classList.add('show');
}

function closeColorModal() {
    colorModal.classList.remove('show');
    currentColorNoteId = null;
}

function openDeleteModal(noteId) {
    currentDeleteNoteId = noteId;
    
    // Get note title for display
    const noteCard = document.querySelector(`[data-note-id="${noteId}"]`);
    const noteTitle = noteCard ? noteCard.querySelector('.note-header h3').textContent : 'Unknown Note';
    document.getElementById('deleteNoteTitle').textContent = noteTitle;
    
    deleteModal.classList.add('show');
}

function closeDeleteModal() {
    deleteModal.classList.remove('show');
    currentDeleteNoteId = null;
}

// User menu functions
function toggleUserMenu() {
    userDropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userMenu = document.querySelector('.user-menu');
    if (!userMenu.contains(event.target)) {
        userDropdown.classList.remove('show');
    }
});

// Color functions
function setColor(color) {
    document.getElementById('noteColor').value = color;
}

function changeNoteColor(color) {
    if (!currentColorNoteId) return;
    
    console.log('Changing color for note:', currentColorNoteId, 'to:', color); // Debug log
    
    fetch('api/simple_test.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'updateColor',
            id: currentColorNoteId,
            color: color
        })
    })
    .then(response => {
        console.log('Color update response status:', response.status); // Debug log
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Color update API response:', data); // Debug log
        if (data.success) {
            const noteCard = document.querySelector(`[data-note-id="${currentColorNoteId}"]`);
            if (noteCard) {
                noteCard.style.backgroundColor = color;
            }
            closeColorModal();
            showNotification('Note color updated successfully!', 'success');
        } else {
            showNotification('Failed to update note color: ' + (data.message || 'Unknown error'), 'error');
            console.error('Color update API error:', data);
        }
    })
    .catch(error => {
        console.error('Color update error:', error);
        showNotification('Color update error: ' + error.message, 'error');
    });
}

// Note CRUD functions
function editNote(noteId) {
    openNoteModal(noteId);
}

function deleteNote(noteId) {
    openDeleteModal(noteId);
}

function confirmDelete() {
    if (!currentDeleteNoteId) return;
    
    fetch('api/simple_test.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'delete',
            id: currentDeleteNoteId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const noteCard = document.querySelector(`[data-note-id="${currentDeleteNoteId}"]`);
            if (noteCard) {
                // Smooth removal animation
                noteCard.style.transform = 'scale(0.8)';
                noteCard.style.opacity = '0';
                noteCard.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    noteCard.remove();
                    checkEmptyState();
                }, 300);
            }
            closeDeleteModal();
            showNotification('Note deleted successfully!', 'success');
        } else {
            showNotification('Failed to delete note: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred: ' + error.message, 'error');
    });
}

// Form submission
noteForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(noteForm);
    const noteId = document.getElementById('noteId').value;
    
    const data = {
        action: noteId ? 'update' : 'create',
        title: formData.get('title'),
        content: formData.get('content'),
        color: formData.get('color')
    };
    
    if (noteId) {
        data.id = noteId;
    }
    
    console.log('Sending data:', data); // Debug log
    
    fetch('api/simple_test.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug log
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(result => {
        console.log('API response:', result); // Debug log
        if (result.success) {
            closeNoteModal();
            showNotification(noteId ? 'Note updated successfully!' : 'Note created successfully!', 'success');
            // Delay reload to show notification
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification('Failed to save note: ' + (result.message || 'Unknown error'), 'error');
            console.error('API error:', result);
        }
    })
    .catch(error => {
        console.error('Network/Parse error:', error);
        showNotification('Connection error: ' + error.message, 'error');
    });
});

// Helper functions
function rgbToHex(rgb) {
    if (!rgb) return '#ffffff';
    
    // Handle hex colors (already in correct format)
    if (rgb.startsWith('#')) {
        return rgb;
    }
    
    // Handle rgb() format
    const result = rgb.match(/\d+/g);
    if (result && result.length >= 3) {
        const r = parseInt(result[0]);
        const g = parseInt(result[1]);
        const b = parseInt(result[2]);
        return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }
    
    return '#ffffff';
}

function checkEmptyState() {
    const notesGrid = document.getElementById('notesGrid');
    const noteCards = notesGrid.querySelectorAll('.note-card');
    
    if (noteCards.length === 0) {
        notesGrid.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-sticky-note"></i>
                <h3>No notes yet</h3>
                <p>Create your first note to get started!</p>
                <button class="btn btn-primary" onclick="openNoteModal()">
                    <i class="fas fa-plus"></i> Create Note
                </button>
            </div>
        `;
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Add styles
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '12px 20px',
        borderRadius: '8px',
        color: 'white',
        fontWeight: '500',
        zIndex: '3000',
        transform: 'translateX(400px)',
        transition: 'transform 0.3s ease'
    });
    
    // Set background color based on type
    if (type === 'success') {
        notification.style.background = '#28a745';
    } else if (type === 'error') {
        notification.style.background = '#dc3545';
    } else {
        notification.style.background = '#667eea';
    }
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    if (event.target === noteModal) {
        closeNoteModal();
    }
    if (event.target === colorModal) {
        closeColorModal();
    }
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
});

// Close modals on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        if (noteModal.classList.contains('show')) {
            closeNoteModal();
        }
        if (colorModal.classList.contains('show')) {
            closeColorModal();
        }
        if (deleteModal.classList.contains('show')) {
            closeDeleteModal();
        }
    }
});
