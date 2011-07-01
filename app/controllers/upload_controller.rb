class UploadController < ApplicationController
  require 'open-uri'

  def qr
    qr_code = params[:id] + ".qrcode"

    file = open('http://www.konreu.com/dcheqr/upload_qr_code.php?qr=' + qr_code, 'User-Agent' => 'yourmom')
    contents = file.read
    render :text => contents
  end

end
