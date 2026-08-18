/**
 * ============================================================================
 * SkillSwap - Main JavaScript
 * ============================================================================
 * 
 * jQuery-based JavaScript for interactive functionality:
 * - AJAX chat polling
 * - Notification polling
 * - Form validation
 * - Dynamic skill dropdowns
 * - Rating inputs
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

$(document).ready(function() {
    // =====================================================================
    // GLOBAL VARIABLES
    // =====================================================================
    const BASE_URL = window.location.protocol + '//' + window.location.host + '/skillswap';
    
    // =====================================================================
    // CHAT FUNCTIONALITY
    // =====================================================================
    
    /**
     * Initialize chat polling if on conversation page
     */
    if ($('#chat-container').length > 0) {
        const otherUserId = $('#chat-container').data('user-id');
        let lastMessageId = $('#chat-container').data('last-id') || 0;
        let isTyping = false;
        
        // Poll for new messages every 3 seconds
        const chatPollInterval = setInterval(function() {
            pollNewMessages(otherUserId, lastMessageId);
        }, 3000);
        
        // Send message on form submit
        $('#chat-form').on('submit', function(e) {
            e.preventDefault();
            sendMessage(otherUserId);
        });
        
        // Send on Enter key (Shift+Enter for new line)
        $('#message-input').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                $('#chat-form').submit();
            }
        });
        
        // Auto-resize textarea
        $('#message-input').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Scroll to bottom on load
        scrollToBottom();
    }
    
    /**
     * Poll for new messages via AJAX
     */
    function pollNewMessages(userId, lastId) {
        $.ajax({
            url: BASE_URL + '/ajax/messages.php',
            type: 'GET',
            data: {
                action: 'poll',
                user_id: userId,
                last_id: lastId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.count > 0) {
                    response.messages.forEach(function(msg) {
                        appendMessage(msg);
                        lastMessageId = msg.id;
                    });
                    scrollToBottom();
                    playNotificationSound();
                }
            },
            error: function(xhr, status, error) {
                console.error('Chat poll error:', error);
            }
        });
    }
    
    /**
     * Send message via AJAX
     */
    function sendMessage(receiverId) {
        const input = $('#message-input');
        const content = input.val().trim();
        
        if (!content) return;
        
        // Disable input while sending
        input.prop('disabled', true);
        $('#send-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.ajax({
            url: BASE_URL + '/ajax/messages.php',
            type: 'POST',
            data: {
                action: 'send',
                receiver_id: receiverId,
                content: content
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Append sent message immediately
                    appendMessage({
                        id: response.message_id,
                        sender_id: $('#chat-container').data('my-id'),
                        content: content.replace(/\n/g, '<br>'),
                        created_at: 'Just now',
                        is_me: true
                    });
                    
                    input.val('');
                    input.css('height', 'auto');
                    scrollToBottom();
                } else {
                    alert('Failed to send message: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Send message error:', error);
                alert('Failed to send message. Please try again.');
            },
            complete: function() {
                input.prop('disabled', false);
                $('#send-btn').prop('disabled', false).html('<i class="bi bi-send-fill"></i>');
                input.focus();
            }
        });
    }
    
    /**
     * Append message to chat container
     */
    function appendMessage(msg) {
        const isMe = msg.is_me;
        const alignment = isMe ? 'sent' : 'received';
        const bgClass = isMe ? 'bg-primary text-white' : 'bg-white';
        
        const html = `
            <div class="chat-message ${alignment} ${bgClass} animate-slide-in" data-id="${msg.id}">
                <div class="message-content">${msg.content}</div>
                <div class="chat-time text-end">
                    <small>${msg.created_at}</small>
                </div>
            </div>
        `;
        
        $('#chat-messages').append(html);
    }
    
    /**
     * Scroll chat to bottom
     */
    function scrollToBottom() {
        const container = $('#chat-container');
        container.scrollTop(container[0].scrollHeight);
    }
    
    // =====================================================================
    // NOTIFICATION POLLING
    // =====================================================================
    
    /**
     * Poll for unread notifications every 10 seconds
     */
    if ($('.navbar').length > 0) {
        const notifPollInterval = setInterval(function() {
            updateNotificationCount();
        }, 10000);
        
        // Initial count
        updateNotificationCount();
    }
    
    /**
     * Update notification badge count
     */
    function updateNotificationCount() {
        $.ajax({
            url: BASE_URL + '/ajax/notifications.php',
            type: 'GET',
            data: { action: 'unread_count' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const badge = $('#notification-badge');
                    if (response.count > 0) {
                        badge.text(response.count).show();
                    } else {
                        badge.hide();
                    }
                }
            }
        });
    }
    
    // =====================================================================
    // SKILL CATEGORY DROPDOWN
    // =====================================================================
    
    /**
     * Load skills when category changes
     */
    $('#category-select').on('change', function() {
        const categoryId = $(this).val();
        const skillSelect = $('#skill-select');
        
        if (!categoryId) {
            skillSelect.html('<option value="">Select Category First</option>').prop('disabled', true);
            return;
        }
        
        skillSelect.prop('disabled', true).html('<option>Loading...</option>');
        
        $.ajax({
            url: BASE_URL + '/ajax/skills.php',
            type: 'GET',
            data: {
                action: 'get_by_category',
                category_id: categoryId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Select Skill</option>';
                    response.skills.forEach(function(skill) {
                        options += `<option value="${skill.id}">${skill.name}</option>`;
                    });
                    skillSelect.html(options).prop('disabled', false);
                } else {
                    skillSelect.html('<option value="">No skills found</option>').prop('disabled', true);
                }
            },
            error: function() {
                skillSelect.html('<option value="">Error loading skills</option>').prop('disabled', true);
            }
        });
    });
    
    // =====================================================================
    // STAR RATING INPUT
    // =====================================================================
    
    /**
     * Handle star rating clicks
     */
    $('.rating-input').on('change', function() {
        const rating = $(this).val();
        $('#rating-value').text(rating + ' out of 5');
    });
    
    $('.rating-label').on('mouseenter', function() {
        const value = $(this).data('value');
        highlightStars(value);
    });
    
    $('.rating-container').on('mouseleave', function() {
        const checkedValue = $('.rating-input:checked').val() || 0;
        highlightStars(checkedValue);
    });
    
    function highlightStars(value) {
        $('.rating-label').each(function() {
            const starValue = $(this).data('value');
            if (starValue <= value) {
                $(this).find('i').removeClass('bi-star').addClass('bi-star-fill');
            } else {
                $(this).find('i').removeClass('bi-star-fill').addClass('bi-star');
            }
        });
    }
    
    // =====================================================================
    // CONFIRM ACTIONS
    // =====================================================================
    
    /**
     * Confirm before delete/decline actions
     */
    $('[data-confirm]').on('click', function(e) {
        const message = $(this).data('confirm');
        if (!confirm(message)) {
            e.preventDefault();
        }
    });
    
    // =====================================================================
    // AUTO-DISMISS ALERTS
    // =====================================================================
    
    /**
     * Auto-dismiss flash messages
     */
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow', function() {
            $(this).alert('close');
        });
    }, 5000);
    
    // =====================================================================
    // FILE INPUT PREVIEW
    // =====================================================================
    
    /**
     * Show image preview on file select
     */
    $('input[type="file"][data-preview]').on('change', function() {
        const previewId = $(this).data('preview');
        const file = this.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(previewId).attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });
    
    // =====================================================================
    // TOOLTIPS & POPOVERS
    // =====================================================================
    
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // =====================================================================
    // SMOOTH SCROLL
    // =====================================================================
    
    /**
     * Smooth scroll to anchor links
     */
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 500);
        }
    });
    
    // =====================================================================
    // LOADING SPINNER
    // =====================================================================
    
    /**
     * Show loading spinner on form submit
     */
    $('form').on('submit', function() {
        const submitBtn = $(this).find('[type="submit"]');
        if (!submitBtn.prop('disabled')) {
            submitBtn.prop('disabled', true);
            if (!submitBtn.find('.spinner-border').length) {
                const originalText = submitBtn.html();
                submitBtn.data('original-text', originalText);
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
            }
        }
    });
    
    // =====================================================================
    // NOTIFICATION SOUND
    // =====================================================================
    
    /**
     * Play subtle notification sound (optional)
     */
    function playNotificationSound() {
        // Only play if user has interacted with page
        if (document.hasFocus()) return;
        
        // Create a simple beep using Web Audio API
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            // Silently fail if audio not supported
        }
    }
    
    // =====================================================================
    // CHARACTER COUNTER
    // =====================================================================
    
    /**
     * Character counter for textareas
     */
    $('[data-max-length]').on('input', function() {
        const maxLength = $(this).data('max-length');
        const currentLength = $(this).val().length;
        const counter = $($(this).data('counter'));
        
        counter.text(currentLength + ' / ' + maxLength);
        
        if (currentLength > maxLength) {
            counter.addClass('text-danger');
        } else {
            counter.removeClass('text-danger');
        }
    });
    
    // =====================================================================
    // SEARCH AUTOCOMPLETE (Simple)
    // =====================================================================
    
    /**
     * Simple search debounce
     */
    let searchTimeout;
    $('[data-search]').on('input', function() {
        clearTimeout(searchTimeout);
        const form = $(this).closest('form');
        searchTimeout = setTimeout(function() {
            form.submit();
        }, 500);
    });
    
});