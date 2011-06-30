# Be sure to restart your server when you modify this file.

# Your secret key for verifying cookie session data integrity.
# If you change this key, all old sessions will become invalid!
# Make sure the secret is at least 30 characters and all random, 
# no regular words or you'll be exposed to dictionary attacks.
ActionController::Base.session = {
  :key         => '_HEQR_session',
  :secret      => 'c9110bb1654bfb10d09d42fe66b25282492cd80f10a6b7d66a773d6e0c433a12d89f497ae5027241158a945730f355be721fa7e676f3d07d8d79f9471ac177f9'
}

# Use the database for sessions instead of the cookie-based default,
# which shouldn't be used to store highly confidential information
# (create the session table with "rake db:sessions:create")
# ActionController::Base.session_store = :active_record_store
