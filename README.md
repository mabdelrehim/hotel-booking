# Hotel-Booking
A hotel booking service website. (web development, database design)


Login page:
We check first if user logged in before then we redirect the user to the corresponding home page.
If not logged in we fill in the forum then we check on type to know in which table in database we need to look in 
If type is user : select the user from customer table to obtain the ID of the user then we check the suspended table by the ID of the user 
If the ID was present in the suspended table we check for duration of suspension if 7 days has passed the user is deleted from the suspended table 
Else the user is redirected to customer page 

If the checked in is a hotel a query is made to obtain id of hotel and if present he is redirected to hotel page 
In hotel page a query is made by hotel id to check if suspended 
In case of suspension a message appear telling that the hotel rooms won’t appear in the search result of customers

If the checked in is a broker a query is made to obtain id of broker and if present he is redirected to broker page 

Customer page :
First we check if the logged in is a customer the we check wether he is suspended or not .In case of suspension he is redirected to suspension page 
If he is not suspended he is left

After that we check if the user has any reservations that he has not checked in if the query returned with any values the customer is redirected to suspended page

In the customer page we have a variable called do that is used to dynamically display a certain page
If do = search then search forum is displayed 
If do = allReservations a query is made to obtain hotel information by preforming inner join of reservation and hotel 
A rate button is displayed if the reservation’s from date has passed and if it didn’t pass a cancel button 

If rate button is pressed client is redirected to rate page where the page receives hotel id and customer id

Rate page :
Takes numeric input from user and insert that input into rate table and updates the avg rating of hotel by avg of rate relation on the attribute number of stars 
After the rating is finished the customer is returned to customer page


Found hotles page :
The query is posted from customer page
A search query outputs all rooms that meet the user’s search qriteria and have no ongoing reservations on them or have ongoing reservations that don’t contradict with the user’s dates 
Check foundhotles.php page from lina 51 to line 105 for full query code
Then the output of this query is displayed in a table with a button next to each entry called make reservation
If the reservation button is pressed customer is redirected to customer.php and put in the do variable the reservation set and puts all information of selected room in get array
Then insert the reservation info into reservation table and obtain id and put it in pending reservations

Then the website calculates the total price by first calculating number of rooms * number of rooms reserved and if the customer is eligible for discount then it is applied

Hotel reservation page :
All the entered hotel information are sent to database and hotel ID is put into the pending hotel requests relation 

User registration page :
Register a new user with entered information only if username and email are not duplicates in database

Hotel page :
If hotel is still pending approval then redirect to login.php
Then check if the hotel is suspended then display a message that this hotel won’t be appearing in the search results
Hotel can see all check-ins that were made or all reservations that have been approved and not cancelled or all the reservations that should be checked in today 
For reservations that need to be checked-in today the hotel can press a button and the reservation is added to the check-in relation and then the money owed by the hotel to the broker is updated by 9% of the total price of that reservation
The hotel has the option to pay and be unsuspended if suspended by paying the amount due and then update next due date by adding 30 days to the due date 

Broker page :
The broker can see a list of all the hotels with all the money they owe him and suspend hotels that didn’t pay on due date 
The broker can also unsuspend the hotels 


### There are some design flaws in this er-diagram design that my instructor noted. There are some relationships that can be omitted and replaced by attributes and some of the relationships will lead to the possibility of spurious tuples (the "in" and the "to" relationships. See the image file of the er-digram for more details on the design).
