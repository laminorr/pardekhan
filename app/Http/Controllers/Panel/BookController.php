<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        // جدیدترین‌ها اول — ۱۰ ردیف × ۳ ستون در هر صفحه
        $books = Book::with('event')->latest()->paginate(30);

        return view('panel.books.index', compact('books'));
    }
}
