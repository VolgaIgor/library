<?php

class ModuleLibrary {
    
    public static function load( $src ) {
        
        Loader::loadClass( 'Book', $src . '/class/Book.php' );
        Loader::loadClass( 'BookCopy', $src . '/class/BookCopy.php' );
        Loader::loadClass( 'Publisher', $src . '/class/Publisher.php' );
        Loader::loadClass( 'Author', $src . '/class/Author.php' );
        Loader::loadClass( 'Category', $src . '/class/Category.php' );
        
        URL::addPage('/search', $src . '/page/search');
        
        URL::addPage('/bookList', $src . '/page/book_list');
        URL::addPage('/authorList', $src . '/page/author_list');
        URL::addPage('/publisherList', $src . '/page/publisher_list');
        URL::addPage('/yearList', $src . '/page/year_list');
        URL::addPage('/categoryList', $src . '/page/category_list');
        
        URL::addPageRegex('/bookInfo/([0-9]+)', $src . '/page/book_info');
        URL::addPageRegex('/author/([0-9]+)', $src . '/page/author');
        URL::addPageRegex('/publisher/([0-9]+)', $src . '/page/publisher');
        URL::addPageRegex('/year/([0-9]{4})', $src . '/page/year');
        URL::addPageRegex('/category/([0-9]+)', $src . '/page/category');
        
        URL::addAdmin('/createAuthor', $src . '/admin/create_author');
        URL::addAdminRegex('/editAuthor/([0-9]+)', $src . '/admin/edit_author');
        URL::addAdminRegex('/deleteAuthor/([0-9]+)', $src . '/admin/delete_author');
        
        URL::addAdmin('/createPublisher', $src . '/admin/create_publisher');
        URL::addAdminRegex('/editPublisher/([0-9]+)', $src . '/admin/edit_publisher');
        URL::addAdminRegex('/deletePublisher/([0-9]+)', $src . '/admin/delete_publisher');
        
        URL::addAdmin('/createCategory', $src . '/admin/create_category');
        URL::addAdminRegex('/editCategory/([0-9]+)', $src . '/admin/edit_category');
        URL::addAdminRegex('/deleteCategory/([0-9]+)', $src . '/admin/delete_category');
        
        URL::addAdmin('/createBook', $src . '/admin/create_book');
        URL::addAdminRegex('/editBook/([0-9]+)', $src . '/admin/edit_book');
        URL::addAdminRegex('/deleteBook/([0-9]+)', $src . '/admin/delete_book');
        
        URL::addAdminRegex('/bookCopy/([0-9]+)', $src . '/admin/book_copy');
        URL::addAdminRegex('/createBookCopy/([0-9]+)', $src . '/admin/create_book_copy');
        URL::addAdminRegex('/editBookCopy/([0-9]+)', $src . '/admin/edit_book_copy');
        URL::addAdminRegex('/deleteBookCopy/([0-9]+)', $src . '/admin/delete_book_copy');
        
        URL::addPage('/user/addBalance', $src . '/page/add_balance');
        URL::addPage('/user/setting', $src . '/page/user_setting');
        URL::addPageRegex('/user/([0-9]+)', $src . '/page/user');
        URL::addAdmin('/userList', $src . '/admin/user_list');
        URL::addAdminRegex('/editBalance/([0-9]+)', $src . '/admin/edit_balance');
        URL::addAdminRegex('/editUser/([0-9]+)', $src . '/admin/user_setting');
        
        URL::addPageRegex('/rentBook/([0-9]+)', $src . '/page/rent_book');
        URL::addPageRegex('/rentedInfo/([0-9]+)', $src . '/page/rented_info');
        URL::addAdminRegex('/leaseBookCopy/([0-9]+)', $src . '/admin/lease_book_copy');
        
    }
    
}
    