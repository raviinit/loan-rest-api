# loan-rest-api
Technical Assingment to Create Loan Rest APIs

Create REST APIs for the user story, app allows authenticated users to go through a loan repayment after admin approval.

> Use cases:
1) Customer create a loan:
    Customer submit a loan request defining amount and term	
    Example:
        Request amount of 10.000 $ with term 3 on date 7th Feb 2022
        he will generate 3 scheduled repayments:
            14th Feb 2022 with amount 3.333,33 $ 
            21st Feb 2022 with amount 3.333,33 $ 
            28th Feb 2022 with amount 3.333,34 $
        The loan and scheduled repayments will have state PENDING

2) Admin approve the loan:
    Admin change the pending loans to state APPROVED

3) Customer can view loan belong to him:
    Add a policy check to make sure that the customers can view them own loan only.

4) Customer add a repayments:
    Customer add a repayment with amount greater or equal to the scheduled repayment 
    The scheduled repayment change the status to PAID
    If all the scheduled repayments connected to a loan are PAID automatically also the loan become PAID

> Technical Requirements Must have:
    ● Build fully functional REST API without any UI 
    ● README.md should contain all the information that the reviewers need to run and use theapp 
    ● Write code with your teammates in mind: apply the standard code style, readable, easy to review & understand. 
    ● Unit and Features tests are required


-----------------------------
Steps taken at local environment:

> Chosen Laragon
    Apache 2.4
    MySQL 5.7 
	
> Upgraded PHP 7.4 to PHP 8.1 
    From https://windows.php.net/download downloaded PHP 8.1 thread safe since we are using Apache as web server and placed it in Laragon's bin folder
    Note: Laravel 9.0 requires PHP 8.0 or above

> Installed laravel/laravel latest version to develop APIs (https://laravel.com/docs/9.x/installation)
    composer create-project laravel/laravel loan-rest-api

    Laravel/laravel installed at loan-rest-api folder and which should be configured as local.loan-rest-api.com

    - cd into the directory loan-rest-api
    
    - composer update
    
    - set
        DB_DATABASE=loanrestapi
        DB_USERNAME=root
        DB_PASSWORD=
	
    - serve the project locally, we can also use the Laravel Homestead (https://laravel.com/docs/9.x/homestead) virtual machine, Laravel Valet, or the built-in PHP development server i.e. WAMMP, XAMMP, Laragon 
    php -S localhost:8000 -t public

> First create user authentication system for rest apis then loan actions as per the use cases / requirements

> Develop the authentication system and database for the Loan APIs
    i.e. Register, Login, Loan request, Loan approval, Loan details and Loan repayment
    - users, password tables comes by default with installation


> Laravel Sanctum used by default for secure, fast, lightweight authentication system for single-page applications (SPA), mobile applications, and simple, token-based APIs.

    - php artisan make:migration create_loans_table --create=loans
    
    - php artisan make:migration create_weekly_repayments_table --create=weekly_repayments
    
    - php artisan migrate
    
    - php artisan cache:clear
    
    - php artisan config:cache


> Create Models, Controllers, Requests, Responses for Loans, WeeklyRepayments

> Run below

   	- php artisan migrate:reset

	- php artisan migrate
    
    	- composer update
    
    	- composer dump-autoload
	
	
> Create required routes

	- php artisan route:list

    POST            api/loan-repayment ............................. Api\WeeklyRepaymentController@loanRepayment
    POST            api/loan-status ........................................ Api\LoanController@changeLoanStatus
    GET|HEAD        api/loans ........................................... loans.index › Api\LoanController@index
    POST            api/loans ........................................... loans.store › Api\LoanController@store
    GET|HEAD        api/loans/create .................................. loans.create › Api\LoanController@create
    GET|HEAD        api/loans/{loan} ...................................... loans.show › Api\LoanController@show
    PUT|PATCH       api/loans/{loan} .................................. loans.update › Api\LoanController@update
    DELETE          api/loans/{loan} ................................ loans.destroy › Api\LoanController@destroy
    GET|HEAD        api/loans/{loan}/edit ................................. loans.edit › Api\LoanController@edit
    POST            api/login ................................................. Api\AuthUserController@loginUser
    POST            api/logout ............................................... Api\AuthUserController@logoutUser
    POST            api/register ........................................... Api\AuthUserController@registerUser
  
> Test the Application flow with various scenarios and/or required end points / APIs

    	- /                         >                           - GET

	- /api/register             > User Register             - POST
    
    	- /api/login                > User Login                - POST
    
    	- /api/loans                > Create a loan request     - POST
    
    	- /api/loans                > View loan details         - GET
    
    	- /api/loan-status          > Loan approval             - POST
    
    	- /api/loan-repayment       > Loan repayment            - POST
    
    	- /api/logout               > User Logout               - POST


> Testing using POSTMAN. Please follow the below process

    Step 1: User creation - /api/register
        Required fields are name, email, password & confirm_password

    Step 2: Login using API - /api/login
        Required fields are email and password
        Get the token here.

    --
    Set the POSTMAN parameters as below.
    Headers
    Authorization   : Bearer token value
    Accept          : application/json

    Or set the Auth
    --

    Step 3: Apply loan (POST method) - /api/loans
        Required fields are - loan_amount and loan_term, term should be in weeks
	
    Step 4: Approve the loan by Admin - /api/loan-status
        Required fields are - loan_id, approval_status, here loan_id to which we should change it to status as approved
        Admin: admin user need to set 'is_admin' to 1 in users table (setting the same user)

    Step 5: Loan Repayment - /api/loan-repayment
        Required fields are - loan_id, amount and loan amount should be same as weekly emi amount

    Step 6: Apply loan (GET method) - /api/loans 
        Required fields are - not required, this would retrieves the user's loan details

    Step 7: Logout user - /api/logout
