This is the source code for the [Game of Bands Song Depository][gob].

  [gob]: http://gameofbands.co/

## File structure

The code is organized like this

    index.php               # main entry point

    stylesheet.css          # main site - styling
    images/                 #           - image resources
    
    lib/                    # external libraries, stuff that we didn't write ourselves
    admin/                  # source code for the admin section of the site
    src/                    # source code for the public section of the site
        query.php           # library for querying and displaying songs
        view_*.php          # individual views of the song data base
        secrets.php         # wrapper for the credentials on the server
        secrets_*.php       # server credentials
        gob_user.php        # Common User Functions
        login_request.php   # Request user log in before continuing
        user_dashboard.php  # User administration dashboard
        user_submitsong.php # Allows uer to submit song for current round

    login.php               # log in via reddit OAuth
    dashboard.php           # User Admin Dashboard
    
    view*.php               # Legacy file support for
    display*.php            # Google until reindex occurs
