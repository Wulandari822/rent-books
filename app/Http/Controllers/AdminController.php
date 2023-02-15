<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
      $bookCount = Book::count();
      $categoryCount = Category::count();
      $userCount = User::count();
        return view('admin.dashboard', ['book_count' => $bookCount, 'category_count' => $categoryCount, 'user_count' => $userCount]);

    }

    public function users()
    {
        $users = User::where('role_id', 2)->where('status', 'active')->get();
        return view('admin.user', ['users' => $users]);
    }

    public function category()
    {
        $categories = Category::all();
        return view('admin.category', ['categories' => $categories]);
    }

    public function books()
    {
        $book = Book::all();
        return view('admin.books', ['book'=> $book]);
    }

    public function rents()
    {
        return view('admin.rents');
    }

    public function categoryAdd(){
        return view('admin.add-category');
    }

    public function categoryStore(Request $request){
        //validate data masuk apa engga
        $validated = $request->validate([
            'name' => 'required|unique:categories',
        ]);

       //memasukkan data ke database kita
       $category = Category::create($request->all());
       return redirect('category')->with('status', 'Category Added Successfully.');
    }

    public function categoryEdit($slug){
        $category = Category::where('slug', $slug)->first();
        return view ('admin.edit-category', ['category' => $category]);
    }

    public function categoryUpdate(Request $request, $slug){
        $validated = $request->validate([
            'name' => 'required|unique:categories',
        ]);

        $category = Category::where('slug', $slug)->first();
        $category->update($request->all());
        $category->slug = null;
        return redirect ('category')->with('status', 'Category Updated Successfully.');
    }

    public function categoryDestroy($slug){

        $category = Category::where('slug', $slug)->first();
        $category->delete();
        return redirect ('category')->with('status', 'Category Deleted Successfully.');

    }

    //menampilkan books
    public function booksAdd(){
        $categories = Category::all();
        return view ('admin.book.add-books', ['categories' => $categories]);
    }

    public function booksStore(Request $request){
        //validate data masuk apa engga
        $validated = $request->validate([
            'book_code' => 'required|unique:books',
            'title' => 'required',
        ]);

        $newName = '';
        

        if($request->file('image')){
            $extension = $request->file('image')->getClientOriginalExtension();
            $newName = $request->title.'-'.now()->timestamp.'.'.$extension;
            $request->file('image')->storeAs('cover', $newName);
        }

        $request['cover'] = $newName;
        $book = Book::create($request->all());
        $book->categories()->sync($request->categories);
        return redirect('books')->with('status', 'Book Added Successfully.');
    }

    public function booksEdit($slug){
        $books = Book::where('slug', $slug)->first();
        $categories = Category::all();
        return view ('admin.book.edit-books', ['books' => $books, 'categories' => $categories]);
    }

    public function booksUpdate(Request $request, $slug){
        if($request->file('image')){
            $extension = $request->file('image')->getClientOriginalExtension();
            $newName = $request->title.'-'.now()->timestamp.'.'.$extension;
            $request->file('image')->storeAs('cover', $newName);
            $request['cover'] = $newName;
        }
        $books = Book::where('slug', $slug)->first();
        $books->update($request->all());
        if($request->categories){
            $books->categories()->sync($request->categories);
        }
        return redirect('books')->with('status', 'Books Updated Successfully.');
    }

    public function booksDestroy($slug){
        $books = Book::where('slug', $slug )->first();
        $books->delete();
        return redirect('books')->with('status', 'Books Delete Successfullly.');
    }

    //user 
    public function usersRegistered(){
        $usersRegistered = User::where('role_id', 2)->where('status', 'inactive')->get();
        return view('admin.users.registered-user', ['usersRegistered' => $usersRegistered]);
    }
    public function usersDetail($slug){
        $user = User::where('slug', $slug)->first();
        return view ('admin.users.user-detail', ['user' => $user]);
    }
    
    public function usersApprove($slug){
        $users = User::where('slug', $slug)->first();
        $users->status = 'active';
        $users->save();
        return redirect ('users-detail/'.$slug)->with('status', 'User Approved Successfully.');
     }

     public function usersBan($slug){
        $user = User::where('slug', $slug)->first();
        $user->delete();
        return redirect('users')->with('status', 'User Deleted Successfully.');
     }

     public function usersBanned(){
        $usersBanned = User::onlyTrashed()->get();
        return view('admin.users.user-banned', ['usersBanned' => $usersBanned]);
     }

     public function usersRestore($slug){
        $user = User::withTrashed()->where('slug', $slug)->first();
        $user->restore();
        return redirect ('users')->with('status', 'User Restored Successfully.');
     }
}
