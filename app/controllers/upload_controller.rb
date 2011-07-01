class UploadController < ApplicationController
  require 'open-uri'

  def qr
    qr_code = params[:id] + ".qrcode"

    file = open('http://localhost/~conroywhitney/DCHEQR/upload_qr_code.php?qr=' + qr_code)
    contents = file.read
    render :text => contents
  end

end
