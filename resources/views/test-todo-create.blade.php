<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Todo作成テスト</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        #result {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h1>Todo作成APIテスト</h1>

    <form id="todoForm">
        <div class="form-group">
            <label for="difficulty_id">難易度ID:</label>
            <input type="number" id="difficulty_id" name="difficulty_id" required>
            <small>例: 1 (低), 2 (中), 3 (高)</small>
        </div>

        <div class="form-group">
            <label for="content">内容:</label>
            <textarea id="content" name="content" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label for="tag_ids">タグID (カンマ区切り):</label>
            <input type="text" id="tag_ids" name="tag_ids" placeholder="1,2,3">
            <small>例: 1,2,3 (オプション)</small>
        </div>

        <button type="submit">Todo作成</button>
    </form>

    <div id="result"></div>

    <script>
        document.getElementById('todoForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '送信中...';

            try {
                const formData = new FormData(this);
                const data = {
                    difficulty_id: parseInt(formData.get('difficulty_id')),
                    content: formData.get('content'),
                    tag_ids: formData.get('tag_ids') ? formData.get('tag_ids').split(',').map(id => parseInt(id.trim())) : []
                };

                const response = await fetch('/api/todos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    resultDiv.className = 'success';
                    resultDiv.innerHTML = `
                        <h3>作成成功！</h3>
                        <p><strong>ID:</strong> ${result.id}</p>
                        <p><strong>難易度ID:</strong> ${result.difficulty_id}</p>
                        <p><strong>内容:</strong> ${result.content}</p>
                        <p><strong>作成日時:</strong> ${result.created_at}</p>
                        <p><strong>タグID:</strong> ${result.tag_ids.join(', ')}</p>
                    `;
                } else {
                    resultDiv.className = 'error';
                    resultDiv.innerHTML = `
                        <h3>エラー</h3>
                        <p>${result.message || '不明なエラー'}</p>
                        <pre>${JSON.stringify(result, null, 2)}</pre>
                    `;
                }
            } catch (error) {
                resultDiv.className = 'error';
                resultDiv.innerHTML = `
                    <h3>ネットワークエラー</h3>
                    <p>${error.message}</p>
                `;
            }
        });
    </script>
</body>
</html>
