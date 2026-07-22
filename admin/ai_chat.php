<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
include '../config/ai_intelligence.php';
checkAdmin();

$engine = new AIIntelligenceEngine($conn);

// Handle AJAX query
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['question'])) {
    $question = $_POST['question'];
    $response = $engine->processQuery($question, $_SESSION['user_id']);
    echo json_encode($response);
    exit();
}

// Get chat history
$chat_history = $conn->query("SELECT q.question, r.response, r.recommendation, r.confidence, q.created_at 
                              FROM ai_questions q 
                              JOIN ai_responses r ON q.id = r.question_id 
                              WHERE q.user_id = {$_SESSION['user_id']} 
                              ORDER BY q.created_at DESC LIMIT 20");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask AI - Hospital Intelligence | Alfurqan Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .chat-wrapper {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
        }
        .chat-messages {
            height: 500px;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .message {
            margin-bottom: 20px;
            display: flex;
        }
        .message.user {
            justify-content: flex-end;
        }
        .message-content {
            max-width: 70%;
            padding: 15px;
            border-radius: 15px;
        }
        .message.user .message-content {
            background: #667eea;
            color: white;
        }
        .message.ai .message-content {
            background: white;
            border: 1px solid #e9ecef;
        }
        .chat-input {
            padding: 20px;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 10px;
        }
        .suggestion-chips {
            padding: 10px 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            background: white;
            border-bottom: 1px solid #e9ecef;
        }
        .chip {
            padding: 8px 15px;
            background: #e9ecef;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            transition: 0.3s;
        }
        .chip:hover {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="chat-wrapper">
        <div class="chat-header">
            <h4 class="mb-0"><i class="fas fa-robot me-2"></i>AI Hospital Assistant</h4>
            <small>Ask me anything about hospital data and analytics</small>
        </div>
        
        <div class="suggestion-chips">
            <span class="chip" onclick="askQuestion('What is the most common disease this month?')">Most common disease</span>
            <span class="chip" onclick="askQuestion('How many critical patients do we have?')">Critical patients</span>
            <span class="chip" onclick="askQuestion('Which disease is increasing the fastest?')">Fastest growing</span>
            <span class="chip" onclick="askQuestion('Which department is overloaded?')">Department load</span>
        </div>
        
        <div class="chat-messages" id="chatMessages">
            <div class="message ai">
                <div class="message-content">
                    <strong>AI Assistant:</strong> Hello! I'm your AI hospital intelligence assistant. I can analyze hospital data and provide insights. Try asking me about disease trends, patient risks, or department performance.
                </div>
            </div>
            
            <?php while($chat = $chat_history->fetch_assoc()): ?>
            <div class="message user">
                <div class="message-content">
                    <strong>You:</strong> <?= htmlspecialchars($chat['question']) ?>
                </div>
            </div>
            <div class="message ai">
                <div class="message-content">
                    <strong>AI:</strong> <?= htmlspecialchars($chat['response']) ?>
                    <?php if($chat['recommendation']): ?>
                    <div class="mt-2 p-2 bg-light rounded">
                        <small><strong>Recommendation:</strong> <?= htmlspecialchars($chat['recommendation']) ?></small>
                    </div>
                    <?php endif; ?>
                    <small class="text-muted">Confidence: <?= $chat['confidence'] ?>%</small>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="chat-input">
            <input type="text" id="questionInput" class="form-control" placeholder="Ask about hospital data...">
            <button class="btn btn-primary" onclick="submitQuestion()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function askQuestion(question) {
            document.getElementById('questionInput').value = question;
            submitQuestion();
        }

        function submitQuestion() {
            const input = document.getElementById('questionInput');
            const question = input.value.trim();
            
            if (!question) return;
            
            // Add user message
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.innerHTML += `
                <div class="message user">
                    <div class="message-content">
                        <strong>You:</strong> ${question}
                    </div>
                </div>
            `;
            
            input.value = '';
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Send to AI
            fetch('ai_chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `question=${encodeURIComponent(question)}`
            })
            .then(response => response.json())
            .then(data => {
                chatMessages.innerHTML += `
                    <div class="message ai">
                        <div class="message-content">
                            <strong>AI:</strong> ${data.answer}
                            ${data.recommendation ? `<div class="mt-2 p-2 bg-light rounded"><small><strong>Recommendation:</strong> ${data.recommendation}</small></div>` : ''}
                            <small class="text-muted">Confidence: ${data.confidence}%</small>
                        </div>
                    </div>
                `;
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch(error => {
                chatMessages.innerHTML += `
                    <div class="message ai">
                        <div class="message-content text-danger">
                            Error processing your question. Please try again.
                        </div>
                    </div>
                `;
            });
        }

        // Enter key to submit
        document.getElementById('questionInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') submitQuestion();
        });
    </script>
</body>
</html>