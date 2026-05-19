<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>All Documents</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#ffffff;
    font-family:Arial,sans-serif;
    padding:40px;
}

.page-box{
    max-width:1100px;
    margin:auto;
    .documents-grid{
    display:grid;
    grid-template-columns:
    repeat(
        auto-fit,
        minmax(320px,1fr)
    );
    gap:20px;
}
}

.page-title{
    font-size:34px;
    font-weight:bold;
    margin-bottom:30px;
}

.topbar{
    display:flex;
    gap:14px;
    margin-bottom:30px;
}

.new-btn{
    padding:12px 18px;
    background:#4f46e5;
    color:white;
    text-decoration:none;
    border-radius:10px;
    font-weight:bold;
}

.logout-btn{
    padding:12px 18px;
    border:none;
    background:#ef4444;
    color:white;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
}

.document-card{
    background:#ffffff;
    border:1px solid #e5e7eb;
    padding:18px 20px;
    border-radius:14px;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
}

.document-title{
    font-size:20px;
    margin-bottom:12px;
}

.card-actions{
    display:flex;
    gap:12px;
}

.open-btn{
    display:inline-block;
    padding:8px 14px;
    background:#4f46e5;
    color:white;
    text-decoration:none;
    border-radius:8px;
}

.delete-btn{
    padding:10px 16px;
    border:none;
    background:#ef4444;
    color:white;
    border-radius:8px;
    cursor:pointer;
}

.empty-text{
    color:#666;
    margin-top:20px;
}

</style>

</head>

<body>

<div class="page-box">

    <h1 class="page-title">
        All Documents
    </h1>

    <!-- TOPBAR -->

    <div class="topbar">

        <a
            href="/documents/create"
            class="new-btn"
        >
            New Document
        </a>

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

    <!-- DOCUMENT LIST -->

    <div class="documents-grid">

    @forelse($documents as $document)

        <div class="document-card">

            <h3 class="document-title">

                {{ $document->title }}

            </h3>

            <div class="card-actions">

                <a
                    href="/documents/{{ $document->id }}"
                    class="open-btn"
                >
                    Open Document
                </a>

                <form
                    method="POST"
                    action="/documents/{{ $document->id }}"
                >

                    @csrf

                    @method('DELETE')

                    <button
                        type="submit"
                        class="delete-btn"
                    >
                        Delete
                    </button>

                </form>

            </div>

        </div>

    @empty

        <p class="empty-text">

            No documents available.

        </p>

    @endforelse

</div>

</body>
</html>