This is the source code for the [Game of Bands Song Depository][gob].

  [gob]: http://gameofbands.co/

## File structure

The code is organized like this

    index.php         # main entry point

    stylesheet.css    # main site - styling
    images/           #           - image resources
    
    lib/              # external libraries, stuff that we didn't write ourselves
    admin/            # source code for the admin section of the site
    src/              # source code for the public section of the site
        query.php     # library for querying and displaying songs
        view_*.php    # individual views of the song data base
        secrets.php   # wrapper for the credentials on the server
        secrets_*.php # server credentials
		god_user.php  # Common User Functions

    login.php         # log in via reddit OAuth
    dashboard.php     # User Admin Dashboard
	
## To Do
	
	• Auto determine winning song and post round winner thread
		• Set winning bit in song database
		• Can we auto-set css flair?
	
	• Start thinking about how best to handle consolidations / adding new teams
		• Create Add New Team page that posts new team info to team annouce thread
	
	• Create Team Submit Song Form.
		• Save to Song List, but don't show until voting starts
		• Post submitted songs to round completion / vote post
