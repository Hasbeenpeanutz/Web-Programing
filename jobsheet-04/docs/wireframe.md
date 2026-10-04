# Wireframe & User Flow — SIMPUS-Mini

Sub-CPMK: Design the UI/UX of the application (project).

The existing pages (Home, Add/List Books, Add/List Members — Jobsheets 1-3) don't yet include Login, Officer Dashboard, and Borrowing/Return features. This document designs wireframes for those pages before implementation starting in Jobsheet 5 and onwards.

## Actors
- **Guest**: can only view the book catalog (Home, Book List) without signing in.
- **Officer**: signs in to access all CRUD features and borrowing transactions.

## User Flow — Borrow Book

```
[Officer Sign In] -> [Dashboard] -> [Select "New Borrowing" menu]
        -> [Select Member] -> [Select Book (stock > 0)]
        -> [Save] -> [Book stock decreases by 1] -> [Back to Dashboard]
```

## User Flow — Return Book

```
[Dashboard] -> [Select "Return" menu] -> [Search active transaction (member/book)]
        -> [Mark "Returned"] -> [Book stock increases by 1]
        -> [Back to Dashboard]
```

## User Flow — Officer Searches Overdue Loans

```
[Officer Sign In] -> [Dashboard] -> [Select "Members" menu]
        -> [Select Member] -> [View Borrow History]
        -> [Check Borrowing Status]
        -> [Overdue Loan?]
             /        \
           Yes         No
            |           |
     [Show Overdue] [Show Normal History]
            |
     [Handle Overdue Loan]
```

## Identify Additional Edge Cases
- A member cannot be registered if the member number already exists.
- Required member information cannot be left empty.
- A book with zero stock cannot be selected in a borrowing form.
- A borrowing transaction must use an existing member.
- A borrowing transaction must use an existing book.
- An already returned transaction cannot be returned again.
- A member with an overdue loan should be identified when viewing borrowing history.

## Wireframe: Login Page

```
+--------------------------------------+
| SIMPUS-Mini                          |
|--------------------------------------|
|                                      |
|        [ Officer Sign In ]           |
|                                      |
| Username : [______________]          |
| Password : [______________]          |
|                                      |
|             [ Sign In ]              |
|                                      |
| Don't have an account? Sign up       |
+--------------------------------------+
```

## Wireframe: Officer Dashboard

```
+-----------------------------------------------------------------------------+
| SIMPUS-Mini      Home | Books | Members | Borrowing | (Officer Name) Logout |
|-----------------------------------------------------------------------------|
|  [Total Books]   [Total Members]   [Currently Borrowed]                     |
|                                                                             |
|  Quick Actions:                                                             |
|  [ + New Borrowing ]   [ + Return ]                                         |
|                                                                             |
|  Recent Transactions                                                        |
|  --------------------------------------------------                         |
|  Member | Book | Borrow Date | Status                                       |
+-----------------------------------------------------------------------------+
```

## Wireframe: Book Borrowing Form

```
+--------------------------------------+
|         Book Borrowing Form          |
|--------------------------------------|
|  Member : [ dropdown select member ] |
|  Book    : [ dropdown, stock > 0 ]   |
|  Borrow Date : [ auto: today ]       |
|                                      |
|          [  Save Borrowing  ]        |
+--------------------------------------+
```

## Wireframe: Book Return Form

```
+-----------------------------------------+
|              Book Return                |
|-----------------------------------------|
|  Search active transaction:             |
|  [ member name / book title ______]     |
|                                         |
|  Member | Book | Borrow Date | [Return] |
+-----------------------------------------+
```

## Wireframe: Member Borrow History

```
+------------------------------------------------------+
|  Borrow History — Siti Aminah                        |
|------------------------------------------------------|
|  Book            | Borrow    | Return  | Status      |
|  Laskar Pelangi   | 01/07    | 10/07   | Completed   |
|  Bumi Manusia     | 15/07    | -       | Borrowed    |
+------------------------------------------------------+
```

## Wireframe: Register New Member

```
+------------------------------------------------------+
| SIMPUS-Mini Home | Books | Members | Borrowing       |
|------------------------------------------------------|
|                                                      |
|              [ Register New Member ]                 |
|                                                      |
| Member Name  : [____________________________]        |
| Member No.   : [____________________________]        |
| Address      : [____________________________]        |
| Phone        : [____________________________]        |
| Gender       : [ Select Gender ▼ ]                   |
| Email        : [____________________________]        |
|                                                      |
|              [ Save Member ]  [ Cancel ]             |
|                                                      |
+------------------------------------------------------+
```

## Consistency with Existing Design

- Accent colors follow assets/css/style.css.
- Navbar typography and layout remain consistent.
- Forms use the same styling as the existing Add Book and Add Member pages.
- Tables use the existing table design.
- Cards use the existing card design.
- New pages should reuse the existing CSS instead of creating a completely new design.