This is the source code for the [Game of Bands Song Depository][gob].

  [gob]: http://gameofbands.co/

## File structure

The code is organized like this

    index.php         # main entry point
    login.php         # small section for logging in
    lib/              # external libraries, stuff that we didn't write ourselves
    admin/            # source code for the admin section of the site
    src/              # source code for the public section of the site
        view_*.php    # individual views of the song data base
        secrets.php   # wrapper for the credentials on the server
