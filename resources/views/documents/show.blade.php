<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Collaborative Document Editor</title>

@vite(['resources/js/app.js'])

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f1f3f4;
    font-family:Arial,sans-serif;
    padding:24px;
}

.editor-box{
    width:100%;
    max-width:1200px;
    margin:auto;
    background:#fff;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 2px 12px rgba(0,0,0,0.08);
}

.editor-header{
    padding:24px;
    text-align:center;
    border-bottom:1px solid #e5e7eb;
}

.editor-header h1{
    font-size:32px;
    font-weight:bold;
}

.logout-box{
    padding:20px;
}

.logout-btn{
    padding:10px 16px;
    border:none;
    background:#ef4444;
    color:white;
    border-radius:8px;
    cursor:pointer;
}

.title-box{
    padding:20px;
    border-bottom:1px solid #e5e7eb;
}

.title-box label{
    display:block;
    margin-bottom:10px;
    color:#555;
    font-size:16px;
}

#document-title{
    width:100%;
    padding:14px 16px;
    border:1px solid #ddd;
    border-radius:10px;
    font-size:18px;
    outline:none;
}

#toolbar{
    display:flex;
    align-items:center;
    gap:10px;
    padding:16px 20px;
    border-bottom:1px solid #e5e7eb;
    background:#fafafa;
}

#toolbar button{
    width:40px;
    height:40px;
    border:none;
    border-radius:8px;
    background:#fff;
    border:1px solid #ddd;
    cursor:pointer;
    font-size:18px;
}

#toolbar button:hover{
    background:#f3f4f6;
}

#save-status{
    padding:16px 20px;
    color:#16a34a;
    font-weight:bold;
}

#typing-indicator{
    padding:0 20px 20px;
    color:#2563eb;
    font-weight:bold;
}

#editor{
    min-height:500px;
    padding:40px 60px;
    font-size:18px;
    line-height:1.8;
    outline:none;
    white-space:pre-wrap;
    word-break:break-word;
    border-top:1px solid #e5e7eb;
}

#editor:empty::before{
    content:"Start typing here...";
    color:#999;
}

.history-box{
    padding:20px;
    border-top:1px solid #e5e7eb;
    background:#fafafa;
}

.history-box h3{
    margin-bottom:14px;
    font-size:24px;
}

.history-item{
    padding:16px;
    margin-bottom:14px;
    background:#fff;
    border:1px solid #ddd;
    border-radius:10px;
}

.history-preview{
    margin-top:10px;
    line-height:1.6;
    color:#555;
}

.restore-btn{
    margin-top:12px;
    padding:8px 14px;
    border:none;
    background:#2563eb;
    color:white;
    border-radius:8px;
    cursor:pointer;
}

.restore-btn:hover{
    background:#1d4ed8;
}

</style>

</head>

<body>

<div class="editor-box">

    <div class="editor-header">

        <h1>
            Collaborative Document Editor
        </h1>

    </div>

    <!-- LOGOUT -->

    <div class="logout-box">

        <form
            method="POST"
            action="{{ route('logout') }}"
        >

            @csrf

            <button
                type="submit"
                class="logout-btn"
            >
                Logout
            </button>

        </form>

    </div>

    <!-- TITLE -->

    <div class="title-box">

        <label>
            Document Title
        </label>

        <input
            type="text"
            id="document-title"
            value="{{ $document->title }}"
        >

    </div>

    <!-- TOOLBAR -->

    <div id="toolbar">

        <button
            type="button"
            onclick="formatText('bold')"
        >
            <b>B</b>
        </button>

        <button
            type="button"
            onclick="formatText('italic')"
        >
            <i>I</i>
        </button>

        <button
            type="button"
            onclick="formatText('underline')"
        >
            <u>U</u>
        </button>

        <button
            type="button"
            onclick="formatText('insertOrderedList')"
        >
            1.
        </button>

        <button
            type="button"
            onclick="formatText('insertUnorderedList')"
        >
            •
        </button>

    </div>

    <!-- SAVE STATUS -->

    <div id="save-status">
        Saved
    </div>

    <!-- USER TYPING -->

    <div id="typing-indicator"></div>

    <!-- EDITOR -->

    <div
        id="editor"
        contenteditable="true"
        spellcheck="false"
    >{!! $document->content !!}</div>

    <!-- VERSION HISTORY -->

    <div class="history-box">

        <h3>
            Version History
        </h3>

        @foreach($revisions as $revision)

            <div class="history-item">

                <strong>

                    Edited by:

                    {{ $revision->user->name ?? 'Unknown User' }}

                </strong>

                <br>

                <small>
                    {{ $revision->created_at }}
                </small>

                <div class="history-preview">

                    <b>Changed content:</b>

                    <br><br>

                    {!! Illuminate\Support\Str::limit(
                        strip_tags($revision->content),
                        120
                    ) !!}

                </div>

                <!-- RESTORE REVISION -->

                <form
                    method="POST"
                    action="/revision/{{ $revision->id }}/restore"
                >

                    @csrf

                    <button
                        type="submit"
                        class="restore-btn"
                    >
                        Restore Revision
                    </button>

                </form>

            </div>

        @endforeach

    </div>

</div>

<script>

const editor =
document.getElementById(
    'editor'
);

const titleInput =
document.getElementById(
    'document-title'
);

const saveStatus =
document.getElementById(
    'save-status'
);

const typingIndicator =
document.getElementById(
    'typing-indicator'
);

let timeout;

/* ENABLE RICH TEXT */

document.execCommand(
    'styleWithCSS',
    false,
    true
);

/* FORMAT */

function formatText(command)
{
    editor.focus();

    document.execCommand(
        command,
        false,
        null
    );

    autoSave();
}

/* AUTOSAVE */

function autoSave()
{
    saveStatus.innerText =
    'Saving...';

    clearTimeout(timeout);

    timeout = setTimeout(async () => {

        try{

            await fetch(
                '/autosave',
                {

                    method:'POST',

                    headers:{
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN':'{{ csrf_token() }}'
                    },

                    body:JSON.stringify({

                        title:
                        titleInput.value,

                        content:
                        editor.innerHTML

                    })

                }
            );

            saveStatus.innerText =
            'Saved';

        }catch(error){

            saveStatus.innerText =
            'Failed';

        }

    },300);
}

/* EVENTS */

editor.addEventListener(
    'input',
    autoSave
);

titleInput.addEventListener(
    'input',
    autoSave
);

/* REALTIME DOCUMENT */

window.addEventListener(
    'load',

    () => {

        let isTyping = false;

        editor.addEventListener(
            'focus',
            () => {
                isTyping = true;
            }
        );

        editor.addEventListener(
            'blur',
            () => {
                isTyping = false;
            }
        );

        window.Echo
        .channel(
            'document.1'
        )

        .listen(
            '.document.updated',

            (e) => {

                if(!isTyping){

                    editor.innerHTML =
                    e.document.content;

                }

                titleInput.value =
                e.document.title;

            }

        );

    }
);

/* USER TYPING */

let typingTimeoutSend;

editor.addEventListener(

    'keydown',

    () => {

        clearTimeout(
            typingTimeoutSend
        );

        typingTimeoutSend =
        setTimeout(() => {

            fetch(
                '/cursor',
                {

                    method:'POST',

                    headers:{
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN':'{{ csrf_token() }}'
                    },

                    body:JSON.stringify({

                        typing:true

                    })

                }

            );

        },200);

    }

);

/* RECEIVE TYPING */

window.Echo
.channel('document.1')

.listen(

    '.cursor.moved',

    () => {

        typingIndicator.innerText =
        'Another user is typing...';

        clearTimeout(
            window.typingTimeout
        );

        window.typingTimeout =
        setTimeout(() => {

            typingIndicator.innerText =
            '';

        },1200);

    }

);

</script>

</body>
</html>