// services/attendeeService.js
import { API_BASE_URL, getAuthHeaders } from '~/utils/api'

export const fetchEventAttendees = async (organizationSlug, eventId) => {
  return await $fetch(`${API_BASE_URL}/${organizationSlug}/admin/events/${eventId}/attendees`, {
    headers: getAuthHeaders()
  })
}

export const deleteAttendee = async (organizationSlug, eventId, attendeeId) => {
  return await $fetch(`${API_BASE_URL}/${organizationSlug}/admin/events/${eventId}/attendees/${attendeeId}`, {
    method: 'DELETE',
    headers: getAuthHeaders()
  })
}