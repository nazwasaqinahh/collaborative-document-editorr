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

.title-box{
    padding:20px;
    border-bottom:1px solid #e5e7eb;
}

.title-box label{
    display:block;
    margin-bottom:10px;
    font-size:16px;
    color:#555;
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
    width:38px;
    height:38px;
    border:none;
    border-radius:8px;
    background:#fff;
    font-size:18px;
    cursor:pointer;
}

#toolbar button:hover{
    background:#f3f4f6;
}

#save-status{
    padding:16px 20px;
    border-bottom:1px solid #e5e7eb;
    font-size:15px;
    font-weight:bold;
    color:#16a34a;
}

#typing-status{
    padding:10px 20px;
    font-size:14px;
    color:#666;
}

#editor{
    min-height:500px;
    padding:40px 60px;
    font-size:18px;
    line-height:1.8;
    outline:none;
    white-space:pre-wrap;
    text-align:left;
    word-break:break-word;
}

#editor:empty::before{
    content:"Start typing here...";
    color:#999;
}

</style>

</head>

<body>

<div class="editor-box">

    <div class="editor-header">
        <h1>Collaborative Document Editor</h1>
    </div>

    <div class="title-box">

        <label>Document Title</label>

        <input
            type="text"
            id="document-title"
            value="{{ $document->title }}"
        >

    </div>

    <div id="toolbar">

        <button
            type="button"
            onclick="formatDoc('bold')"
        >
            <b>B</b>
        </button>

        <button
            type="button"
            onclick="formatDoc('italic')"
        >
            <i>I</i>
        </button>

        <button
            type="button"
            onclick="formatDoc('underline')"
        >
            <u>U</u>
        </button>

        <button
            type="button"
            onclick="formatDoc('insertOrderedList')"
        >
            1.
        </button>

        <button
            type="button"
            onclick="formatDoc('insertUnorderedList')"
        >
            •
        </button>

    </div>

    <div id="save-status">
        Saved
    </div>

    <div id="typing-status"></div>

    <div
        id="editor"
        contenteditable="true"
        spellcheck="false"
    >{!! $document->content !!}</div>

</div>

<script>

const editor =
    document.getElementById('editor');

const titleInput =
    document.getElementById('document-title');

const saveStatus =
    document.getElementById('save-status');

const typingStatus =
    document.getElementById('typing-status');

let timeout;

let typingTimeout;

/* FORMAT */

function formatDoc(command)
{
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

                '/documents/{{ $document->id }}/autosave',

            {

                method:'POST',

                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },

                body:JSON.stringify({

                    title:titleInput.value,

                    content:editor.innerHTML

                })

            });

            saveStatus.innerText =
                'Saved';

        }catch(error){

            saveStatus.innerText =
                'Failed';

        }

    },200);
}

/* EVENTS */

editor.addEventListener(
    'input',
    () => {

        typingStatus.innerText =
            'Collaborator is typing...';

        clearTimeout(
            typingTimeout
        );

        typingTimeout =
            setTimeout(() => {

                typingStatus.innerText = '';

            },1000);

        autoSave();

    }
);

titleInput.addEventListener(
    'input',
    autoSave
);

</script>

<!-- REALTIME -->

<script>

window.addEventListener(
    'load',
    () => {

        let isTyping = false;

        editor.addEventListener(
            'input',
            () => {

                isTyping = true;

                clearTimeout(
                    window.typingTimer
                );

                window.typingTimer =
                    setTimeout(() => {

                        isTyping = false;

                    },500);

            }
        );

        window.Echo
        .channel(
            'document.{{ $document->id }}'
        )

        .listen(
            '.document.updated',

            (e) => {

                if(!isTyping){

                    if(
                        editor.innerHTML !==
                        e.document.content
                    ){

                        const selection =
                            window.getSelection();

                        const range =
                            selection.rangeCount > 0
                            ? selection.getRangeAt(0)
                            : null;

                        editor.innerHTML =
                            e.document.content;

                        if(range){

                            selection.removeAllRanges();

                            selection.addRange(range);
                        }

                    }

                    titleInput.value =
                        e.document.title;

                }

            }

        );

    }
);

</script>

</body>
</html>